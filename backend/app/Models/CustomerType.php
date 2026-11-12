<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerType extends Model
{
    protected $fillable = [
        'name',
        'new_commission_percent',
        'existing_commission_percent',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'new_commission_percent' => 'decimal:2',
            'existing_commission_percent' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
