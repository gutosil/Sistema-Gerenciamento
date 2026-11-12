<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;

class ContractController extends ResourceController
{
    protected string $modelClass = Contract::class;

    protected array $relationships = ['client', 'salesperson', 'product', 'contractType', 'customerType', 'installments'];

    protected function rules(?Model $record = null): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'salesperson_id' => ['required', 'integer', 'exists:users,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'contract_type_id' => ['nullable', 'integer', 'exists:contract_types,id'],
            'customer_type_id' => ['nullable', 'integer', 'exists:customer_types,id'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'hours_worked' => ['nullable', 'integer', 'min:0'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'entry_amount' => ['sometimes', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['sometimes', 'in:cash,installments'],
            'installments_count' => ['sometimes', 'integer', 'min:1', 'max:120'],
            'status' => ['sometimes', 'in:active,inactive,completed,cancelled'],
            'cancellation_reason' => ['nullable', 'string'],
        ];
    }
}
