<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    /**
     * Display a paginated list of suppliers.
     */
    public function index(Request $request)
    {
        $query = Supplier::latest();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('registration_no', 'ILIKE', "%{$search}%");
            });
        }

        $suppliers = $query->paginate(20)
            ->withQueryString()
            ->through(fn ($s) => [
                'id'              => $s->id,
                'name'            => $s->name,
                'registration_no' => $s->registration_no,
                'phone'           => $s->phone,
                'email'           => $s->email,
                'contact_person'  => $s->contact_person,
                'is_active'       => $s->is_active,
                'master_items_count' => $s->masterItems()->count(),
                'created_at'      => $s->created_at?->toDateString(),
            ]);

        return inertia('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters'   => $request->only(['search']),
            'trashed'   => Supplier::onlyTrashed()->latest()->get(),
        ]);
    }

    /**
     * Store a new supplier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'registration_no' => 'nullable|string|max:100',
            'address'         => 'nullable|string',
            'phone'           => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:255',
            'contact_person'  => 'nullable|string|max:255',
            'is_active'       => 'nullable|boolean',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'registration_no' => 'nullable|string|max:100',
            'address'         => 'nullable|string',
            'phone'           => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:255',
            'contact_person'  => 'nullable|string|max:255',
            'is_active'       => 'nullable|boolean',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    /**
     * Restore a soft-deleted supplier.
     */
    public function restore($id)
    {
        $supplier = Supplier::withTrashed()->findOrFail($id);
        $supplier->restore();
        return redirect()->back()->with('success', 'Rekod telah dipulihkan.');
    }
}
