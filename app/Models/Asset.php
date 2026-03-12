<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
    'asset_tag', 
    'name', 
    'category', 
    'purchase_price', 
    'location', 
    'status',
    'national_code',
    'supplier_name',
    'supplier_address',
    'po_reference',
    'warranty_period',
];
}