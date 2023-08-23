<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FundTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'from_bank_id', 'to_bank_id', 'date', 'note', 'amount',
    ];

    public function fromBank()
    {
        return $this->belongsTo(Bank::class, 'from_bank_id');
    }

    public function toBank()
    {
        return $this->belongsTo(Bank::class, 'to_bank_id');
    }
}
