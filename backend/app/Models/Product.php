<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'new_commission_percent',
        'existing_commission_percent',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'new_commission_percent' => 'decimal:2',
            'existing_commission_percent' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
