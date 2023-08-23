<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id', 'stock_id', 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function items()
    {
        return $this->hasMany(StockReturnItem::class, 'stock_return_id', 'id');
    }
}
