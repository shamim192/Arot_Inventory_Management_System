<?php

namespace App\Models;

use App\Models\Bank;
use App\Models\IncomeCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Income extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'bank_id', 'category_id', 'income_number', 'date', 'note', 'amount',
    ];

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'category_id', 'id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
