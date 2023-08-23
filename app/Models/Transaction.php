<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type', 'flag', 'flagable_id', 'flagable_type', 'bank_id', 'datetime', 'note', 'amount',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    
    public function flagable()
    {
        return $this->morphTo();
    }
}
