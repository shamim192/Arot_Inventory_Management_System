<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id', 'bank_id', 'type', 'date', 'note', 'amount', 'sale_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
