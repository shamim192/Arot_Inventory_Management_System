<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bank_id', 'category_id', 'expense_number', 'date', 'note', 'amount',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id', 'id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
