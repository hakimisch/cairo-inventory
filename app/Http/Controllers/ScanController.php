<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\DoLineItem;
use App\Models\DeliveryOrder;
use App\Models\Asset;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScanController extends Controller
{
    /**
     * Display the scanner page.
     */
    public function index()
    {
        $recentScans = Scan::with('scanner')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($s) => [
                'id'            => $s->id,
                'serial_number' => $s->serial_number,
                'scan_type'     => $s->scan_type,
                'result'        => $s->result,
                'scanner_name'  => $s->scanner?->name ?? 'System',
                'scanned_at'    => $s->scanned_at?->toISOString(),
            ]);

        return inertia('Scanner/Index', [
            'recentScans' => $recentScans,
        ]);
    }

    /**
     * Look up a serial number against all pending DO line items.
     * Returns match info or suggests possible DOs.
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string|max:255',
        ]);

        $serialNumber = trim($request->serial_number);

        // Search for matching line items in pending/partial DOs
        $matches = DoLineItem::where(function ($q) use ($serialNumber) {
                // Direct match
                $q->where('serial_number', $serialNumber)
                  // Or comma-separated list contains this S/N
                  ->orWhere('serial_number', 'LIKE', "%{$serialNumber}%");
            })
            ->whereIn('status', ['pending', 'received'])
            ->with(['deliveryOrder.supplier'])
            ->get()
            ->map(fn ($item) => [
                'id'                => $item->id,
                'do_id'             => $item->delivery_order_id,
                'do_no'             => $item->deliveryOrder->do_no,
                'supplier_name'     => $item->deliveryOrder->supplier?->name ?? 'N/A',
                'description'       => $item->description,
                'brand'             => $item->brand,
                'model'             => $item->model,
                'quantity_ordered'  => $item->quantity_ordered,
                'quantity_received' => $item->quantity_received,
                'status'            => $item->status,
                'category'          => $item->category,
            ]);

        // Also check if this S/N already exists in the assets table
        $existingAsset = Asset::where('serial_number', $serialNumber)->first();

        return response()->json([
            'serial_number'  => $serialNumber,
            'matches'        => $matches,
            'match_count'    => $matches->count(),
            'existing_asset' => $existingAsset ? [
                'id'        => $existingAsset->id,
                'asset_tag' => $existingAsset->asset_tag,
                'name'      => $existingAsset->name,
                'status'    => $existingAsset->status,
            ] : null,
        ]);
    }

    /**
     * Record a scan result.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'serial_number'  => 'required|string|max:255',
            'result'         => 'required|in:match,mismatch,new,duplicate',
            'do_line_item_id' => 'nullable|exists:do_line_items,id',
            'asset_id'       => 'nullable|exists:assets,id',
            'location'       => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'create_asset'   => 'nullable|boolean',
        ]);

        $scan = Scan::create([
            'serial_number'  => $request->serial_number,
            'scan_type'      => 'receive',
            'scanner_user_id' => auth()->id(),
            'scanned_at'     => now(),
            'do_line_item_id' => $request->do_line_item_id,
            'asset_id'       => $request->asset_id,
            'location'       => $request->location,
            'result'         => $request->result,
            'notes'          => $request->notes,
        ]);

        // If matched and we have a line item, update its status
        if ($request->result === 'match' && $request->do_line_item_id) {
            $lineItem = DoLineItem::find($request->do_line_item_id);
            if ($lineItem && $lineItem->status === 'pending') {
                $lineItem->update([
                    'status'           => 'received',
                    'quantity_received' => $lineItem->quantity_ordered,
                    'scan_user_id'     => auth()->id(),
                    'scanned_at'       => now(),
                ]);

                // Check if DO is complete
                $do = $lineItem->deliveryOrder;
                $totalOrdered = $do->lineItems()->sum('quantity_ordered');
                $totalReceived = $do->lineItems()->sum('quantity_received');
                if ($totalReceived >= $totalOrdered) {
                    $do->update(['status' => 'complete']);
                }

                // Auto-create asset if requested
                if ($request->boolean('create_asset')) {
                    $asset = Asset::create([
                        'asset_tag'        => 'AST-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                        'name'             => $lineItem->description,
                        'category'         => $lineItem->category ?? 'General',
                        'brand'            => $lineItem->brand,
                        'model'            => $lineItem->model,
                        'serial_number'    => $lineItem->serial_number,
                        'supplier_name'    => $do->supplier?->name,
                        'do_reference'     => $do->do_no,
                        'po_reference'     => $do->po_reference,
                        'do_line_item_id'  => $lineItem->id,
                        'verified_by_scan' => true,
                        'scan_verified_at' => now(),
                        'asset_type'       => 'inventory',
                        'status'           => 'active',
                        'location'         => 'Pending Assignment',
                        'purchase_price'   => 0,
                        'unit_price'       => 0,
                    ]);

                    $responseExtra = [
                        'asset_created' => true,
                        'asset_tag'     => $asset->asset_tag,
                        'asset_id'      => $asset->id,
                    ];
                }
            }
        }

        $response = [
            'scan' => [
                'id'            => $scan->id,
                'serial_number' => $scan->serial_number,
                'result'        => $scan->result,
                'scanned_at'    => $scan->scanned_at->toISOString(),
            ],
            'message' => $this->resultMessage($request->result),
        ];

        if (isset($responseExtra)) {
            $response = array_merge($response, $responseExtra);
        }

        return response()->json($response);
    }

    /**
     * Get scan history with pagination.
     */
    public function history(Request $request)
    {
        $query = Scan::with('scanner', 'doLineItem.deliveryOrder', 'asset')
            ->latest();

        if ($request->search) {
            $search = $request->search;
            $query->where('serial_number', 'ILIKE', "%{$search}%");
        }

        if ($request->result) {
            $query->where('result', $request->result);
        }

        $scans = $query->paginate(30)
            ->withQueryString()
            ->through(fn ($s) => [
                'id'             => $s->id,
                'serial_number'  => $s->serial_number,
                'scan_type'      => $s->scan_type,
                'result'         => $s->result,
                'scanner_name'   => $s->scanner?->name ?? 'System',
                'scanned_at'     => $s->scanned_at?->toISOString(),
                'do_no'          => $s->doLineItem?->deliveryOrder?->do_no,
                'item_description' => $s->doLineItem?->description,
                'asset_tag'      => $s->asset?->asset_tag,
                'location'       => $s->location,
            ]);

        return inertia('Scanner/History', [
            'scans'   => $scans,
            'filters' => $request->only(['search', 'result']),
        ]);
    }

    private function resultMessage(string $result): string
    {
        return match ($result) {
            'match'     => 'Match found! Line item marked as received.',
            'mismatch'  => 'No matching DO item found for this serial number.',
            'duplicate' => 'This serial number has already been scanned.',
            'new'       => 'New serial number recorded (not yet in any DO).',
            default     => 'Scan recorded.',
        };
    }
}
