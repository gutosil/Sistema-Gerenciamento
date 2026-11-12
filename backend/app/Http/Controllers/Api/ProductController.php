<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ProductController extends ResourceController
{
    protected string $modelClass = Product::class;

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($record?->getKey())],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'new_commission_percent' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'existing_commission_percent' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'active' => ['sometimes', 'boolean'],
        ];
    }
}
