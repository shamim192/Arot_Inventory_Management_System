<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id', 'date', 'challan_number', 'challan_date', 'total_quantity', 'subtotal_amount', 'tax_percent', 'tax_amount', 'discount_amount', 'total_amount',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(StockItem::class, 'stock_id', 'id');
    }
}
