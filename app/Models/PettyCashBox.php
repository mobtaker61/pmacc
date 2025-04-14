<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'currency',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(PettyCashTransaction::class);
    }

    public function getCurrentBalanceAttribute()
    {
        $income = $this->transactions()->where('type', 'income')->sum('irr_amount');
        $expense = $this->transactions()->where('type', 'expense')->sum('irr_amount');
        
        return $income - $expense;
    }

    public function getIrrRateAttribute()
    {
        return (float) Setting::where('key', 'usd_to_irr_rate')->first()?->value ?? 0;
    }

    public function getTryRateAttribute()
    {
        return (float) Setting::where('key', 'try_to_irr_rate')->first()?->value ?? 0;
    }
}
