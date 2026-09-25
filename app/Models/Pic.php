<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pic extends Model
{
    protected $fillable = [
        'name',
        'email' ,
        'match_rule' ,
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function deliveryRecords(): HasMany
    {
        return $this->hasMany(DeliveryRecord::class);
    }
}
