<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListAuxiliarMateriaRequest;
use App\Http\Requests\StoreAuxiliarMateriaRequest;
use App\Http\Requests\UpdateAuxiliarMateriaRequest;
use App\Http\Resources\AuxiliarMateriaResource;
use App\Services\AuxiliarMateriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class AuxiliarMateriaController extends Controller
{
    public function __construct(
        private readonly AuxiliarMateriaService $auxiliarMateriaService
    ) {}

    public function index(ListAuxiliarMateriaRequest $request): AnonymousResourceCollection
    {
        return AuxiliarMateriaResource::collection($this->auxiliarMateriaService->paginate($request->validated()));
    }

    public function store(StoreAuxiliarMateriaRequest $request): JsonResponse
    {
        return (new AuxiliarMateriaResource($this->auxiliarMateriaService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): AuxiliarMateriaResource
    {
        return new AuxiliarMateriaResource($this->auxiliarMateriaService->findOrFail($id));
    }

    public function update(UpdateAuxiliarMateriaRequest $request, string $id): AuxiliarMateriaResource
    {
        return new AuxiliarMateriaResource($this->auxiliarMateriaService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->auxiliarMateriaService->delete($id);

        return response()->json(null, 204);
    }
}
