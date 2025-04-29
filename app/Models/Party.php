<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Party extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'phone',
        'mobile',
        'email',
        'address',
        'description',
        'party_group_id',
    ];

    protected $appends = ['name'];

    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function getOfficialNameAttribute(): string
    {
        if ($this->company_name) {
            return $this->name . ' (' . $this->company_name . ')';
        }
        return $this->name;
    }

    public function partyGroup(): BelongsTo
    {
        return $this->belongsTo(PartyGroup::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PettyCashTransaction::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getTotalPaymentsAttribute(): float
    {
        // Total payments are expenses paid to this party (outgoing money)
        $expensePayments = $this->expenses()->sum('irr_amount') ?? 0;
        
        // Add any 'payment' type transactions (additional outgoing money)
        $transactionPayments = $this->transactions()
            ->where('type', 'payment')
            ->sum('irr_amount') ?? 0;
        
        return $expensePayments + $transactionPayments;
    }

    public function getTotalReceiptsAttribute(): float
    {
        // Total receipts are income received from this party (incoming money)
        return $this->transactions()
            ->where('type', 'receipt')
            ->sum('irr_amount') ?? 0;
    }

    public function getBalanceAttribute(): float
    {
        // Positive balance means the party has contributed more than taken
        // Negative balance means the party has taken more than contributed
        return $this->total_receipts - $this->total_payments;
    }
}
