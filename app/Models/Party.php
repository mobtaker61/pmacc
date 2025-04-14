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
        if ($this->company_name) {
            return $this->company_name;
        }
        return $this->first_name . ' ' . $this->last_name;
    }

    public function partyGroup(): BelongsTo
    {
        return $this->belongsTo(PartyGroup::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PettyCashTransaction::class);
    }

    public function getTotalPaymentsAttribute(): float
    {
        return $this->transactions()
            ->where('type', 'payment')
            ->sum('amount');
    }

    public function getTotalReceiptsAttribute(): float
    {
        return $this->transactions()
            ->where('type', 'receipt')
            ->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_receipts - $this->total_payments;
    }
}
