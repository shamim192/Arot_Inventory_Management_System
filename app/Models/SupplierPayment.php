<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id', 'bank_id', 'type', 'date', 'note', 'amount', 'stock_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
