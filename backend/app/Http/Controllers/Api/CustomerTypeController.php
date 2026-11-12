<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomerType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CustomerTypeController extends ResourceController
{
    protected string $modelClass = CustomerType::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('customer_types')->ignore($record?->getKey())],
            'new_commission_percent' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'existing_commission_percent' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
