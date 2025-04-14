<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'petty_cash_box_id',
        'party_id',
        'type',
        'amount',
        'description',
        'transaction_date',
        'payer_receiver',
        'currency',
        'rate',
        'irr_amount',
        'receipt_image'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'irr_amount' => 'decimal:2'
    ];

    public function pettyCashBox()
    {
        return $this->belongsTo(PettyCashBox::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
