<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserController extends ResourceController
{
    protected string $modelClass = User::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($record?->getKey())],
            'password' => $record ? ['sometimes', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('users')->ignore($record?->getKey())],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['sometimes', Rule::in(['seller', 'finance', 'admin'])],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function destroy(string $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);

        if ($user->contracts()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir um vendedor que possui contratos.',
            ], 409);
        }

        $user->delete();

        return response()->json(null, 204);
    }
}
