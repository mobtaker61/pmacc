<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
} 