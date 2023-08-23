<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleReturnItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_return_id', 'sale_id', 'sale_item_id', 'product_id', 'unit_id', 'unit_quantity', 'quantity', 'unit_price', 'amount', 'actual_quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
