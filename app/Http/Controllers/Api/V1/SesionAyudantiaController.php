<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListSesionAyudantiaRequest;
use App\Http\Requests\StoreSesionAyudantiaRequest;
use App\Http\Requests\UpdateSesionAyudantiaRequest;
use App\Http\Resources\SesionAyudantiaResource;
use App\Services\SesionAyudantiaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class SesionAyudantiaController extends Controller
{
    public function __construct(
        private readonly SesionAyudantiaService $sesionAyudantiaService
    ) {}

    public function index(ListSesionAyudantiaRequest $request): AnonymousResourceCollection
    {
        return SesionAyudantiaResource::collection($this->sesionAyudantiaService->paginate($request->validated()));
    }

    public function store(StoreSesionAyudantiaRequest $request): JsonResponse
    {
        return (new SesionAyudantiaResource($this->sesionAyudantiaService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): SesionAyudantiaResource
    {
        return new SesionAyudantiaResource($this->sesionAyudantiaService->findOrFail($id));
    }

    public function update(UpdateSesionAyudantiaRequest $request, string $id): SesionAyudantiaResource
    {
        return new SesionAyudantiaResource($this->sesionAyudantiaService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->sesionAyudantiaService->delete($id);

        return response()->json(null, 204);
    }

    public function iniciar(string $id): SesionAyudantiaResource
    {
        return new SesionAyudantiaResource($this->sesionAyudantiaService->iniciar($id));
    }

    public function finalizar(string $id): SesionAyudantiaResource
    {
        return new SesionAyudantiaResource($this->sesionAyudantiaService->finalizar($id));
    }

    public function cancelar(string $id): SesionAyudantiaResource
    {
        return new SesionAyudantiaResource($this->sesionAyudantiaService->cancelar($id));
    }
}
