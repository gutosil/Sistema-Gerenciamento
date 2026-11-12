<?php

namespace App\Http\Controllers\Api;

use App\Models\Installment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class InstallmentController extends ResourceController
{
    protected string $modelClass = Installment::class;

    protected array $relationships = ['contract'];

    protected function rules(?Model $record = null): array
    {
        return [
            'contract_id' => ['required', 'integer', 'exists:contracts,id'],
            'number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('installments')->where('contract_id', request()->input('contract_id'))->ignore($record?->getKey()),
            ],
            'due_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'commission_amount' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:pending,paid,cancelled'],
        ];
    }
}
