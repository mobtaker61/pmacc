<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'petty_cash_box_id',
        'expense_group_id',
        'party_id',
        'date',
        'type',
        'amount',
        'currency',
        'irr_amount',
        'rate',
        'description',
        'receipt_image',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'irr_amount' => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    public function pettyCashBox(): BelongsTo
    {
        return $this->belongsTo(PettyCashBox::class);
    }

    public function expenseGroup(): BelongsTo
    {
        return $this->belongsTo(ExpenseGroup::class, 'expense_group_id');
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
} 