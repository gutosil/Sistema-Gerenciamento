<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ResourceController extends Controller
{
    /** @var class-string<Model> */
    protected string $modelClass;

    protected array $relationships = [];

    abstract protected function rules(?Model $record = null): array;

    protected function query(): Builder
    {
        $modelClass = $this->modelClass;

        return $modelClass::query()->with($this->relationships);
    }

    public function index(): JsonResponse
    {
        return response()->json($this->query()->orderByDesc('id')->paginate(100));
    }

    public function store(Request $request): JsonResponse
    {
        $modelClass = $this->modelClass;
        $record = $modelClass::create($request->validate($this->rules()));

        return response()->json($record->load($this->relationships), 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json($this->query()->findOrFail($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $record = $this->query()->findOrFail($id);
        $record->update($request->validate($this->rules($record)));

        return response()->json($record->refresh()->load($this->relationships));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->query()->findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}
