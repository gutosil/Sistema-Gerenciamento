<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $fillable = [
        'client_id',
        'salesperson_id',
        'product_id',
        'contract_type_id',
        'customer_type_id',
        'started_at',
        'ended_at',
        'hours_worked',
        'contact_name',
        'contact_phone',
        'contact_email',
        'entry_amount',
        'total_amount',
        'payment_method',
        'installments_count',
        'status',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
            'entry_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function salesperson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'salesperson_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class);
    }

    public function customerType(): BelongsTo
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }
}
