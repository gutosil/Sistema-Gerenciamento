<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ClientController extends ResourceController
{
    protected string $modelClass = Client::class;

    protected array $relationships = ['customerType'];

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'max:20', Rule::unique('clients')->ignore($record?->getKey())],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'customer_type_id' => ['nullable', 'integer', 'exists:customer_types,id'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function destroy(string $id): JsonResponse
    {
        $client = Client::query()->findOrFail($id);

        if ($client->contracts()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir um cliente que possui contratos.',
            ], 409);
        }

        $client->delete();

        return response()->json(null, 204);
    }
}
