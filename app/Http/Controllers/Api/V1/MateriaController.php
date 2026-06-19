<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListMateriaRequest;
use App\Http\Requests\StoreMateriaRequest;
use App\Http\Requests\UpdateMateriaRequest;
use App\Http\Resources\MateriaResource;
use App\Services\MateriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class MateriaController extends Controller
{
    public function __construct(
        private readonly MateriaService $materiaService
    ) {}

    public function index(ListMateriaRequest $request): AnonymousResourceCollection
    {
        return MateriaResource::collection($this->materiaService->paginate($request->validated()));
    }

    public function store(StoreMateriaRequest $request): JsonResponse
    {
        return (new MateriaResource($this->materiaService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): MateriaResource
    {
        return new MateriaResource($this->materiaService->findOrFail($id));
    }

    public function update(UpdateMateriaRequest $request, string $id): MateriaResource
    {
        return new MateriaResource($this->materiaService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->materiaService->delete($id);

        return response()->json(null, 204);
    }
}
