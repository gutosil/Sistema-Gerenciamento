<?php

namespace App\Http\Controllers\Api;

use App\Models\ContractType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ContractTypeController extends ResourceController
{
    protected string $modelClass = ContractType::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('contract_types')->ignore($record?->getKey())],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
