<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    protected $fillable = [
    'receive_no', 
    'supplier_name', 
    'delivery_order_no', 
    'purchase_order_no', 
    'item_description', 
    'quantity_ordered', 
    'quantity_received', 
    'status',
    // ADD THESE TO FILL KEW.PA-1
    'supplier_address',
    'technical_officer_name',
];
}
