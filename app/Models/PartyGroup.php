<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartyGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function parties(): HasMany
    {
        return $this->hasMany(Party::class);
    }
} 