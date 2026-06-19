<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListDisponibilidadAuxiliarRequest;
use App\Http\Requests\StoreDisponibilidadAuxiliarRequest;
use App\Http\Requests\UpdateDisponibilidadAuxiliarRequest;
use App\Http\Resources\DisponibilidadAuxiliarResource;
use App\Services\DisponibilidadAuxiliarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class DisponibilidadAuxiliarController extends Controller
{
    public function __construct(
        private readonly DisponibilidadAuxiliarService $disponibilidadAuxiliarService
    ) {}

    public function index(ListDisponibilidadAuxiliarRequest $request): AnonymousResourceCollection
    {
        return DisponibilidadAuxiliarResource::collection($this->disponibilidadAuxiliarService->paginate($request->validated()));
    }

    public function store(StoreDisponibilidadAuxiliarRequest $request): JsonResponse
    {
        return (new DisponibilidadAuxiliarResource($this->disponibilidadAuxiliarService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): DisponibilidadAuxiliarResource
    {
        return new DisponibilidadAuxiliarResource($this->disponibilidadAuxiliarService->findOrFail($id));
    }

    public function update(UpdateDisponibilidadAuxiliarRequest $request, string $id): DisponibilidadAuxiliarResource
    {
        return new DisponibilidadAuxiliarResource($this->disponibilidadAuxiliarService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->disponibilidadAuxiliarService->delete($id);

        return response()->json(null, 204);
    }
}
