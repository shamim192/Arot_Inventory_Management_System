<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bank_id', 'date', 'note', 'amount',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
