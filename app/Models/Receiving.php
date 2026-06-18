<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Receiving extends Model
{
    use LogsActivity;

    protected $fillable = [
        'receive_no',
        'supplier_name',
        'supplier_address',
        'purchase_order_no',
        'delivery_order_no',
        'invoice_no',               // No Invois — KEW.PA-1A header
        'item_description',
        'quantity_ordered',
        'quantity_received',
        'unit_price',               // Harga Seunit
        'total_price',              // Jumlah (auto-computed)
        'status',                   // pending | accepted | rejected
        // Signature block — Pegawai Penerima
        'receiver_name',
        'receiver_position',
        // Signature block — Pegawai Bertanggungjawab
        'technical_officer_name',
        'technical_officer_position',
        'damage_description', // NEW
        'notes',              // NEW
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}