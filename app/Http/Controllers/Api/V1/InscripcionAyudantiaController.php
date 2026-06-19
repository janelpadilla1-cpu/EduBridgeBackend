<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListInscripcionAyudantiaRequest;
use App\Http\Requests\StoreInscripcionAyudantiaRequest;
use App\Http\Requests\UpdateInscripcionAyudantiaRequest;
use App\Http\Resources\InscripcionAyudantiaResource;
use App\Services\InscripcionAyudantiaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\RegistrarAsistenciaRequest;

class InscripcionAyudantiaController extends Controller
{
    public function __construct(
        private readonly InscripcionAyudantiaService $inscripcionAyudantiaService
    ) {}

    public function index(ListInscripcionAyudantiaRequest $request): AnonymousResourceCollection
    {
        return InscripcionAyudantiaResource::collection($this->inscripcionAyudantiaService->paginate($request->validated()));
    }

    public function store(StoreInscripcionAyudantiaRequest $request): JsonResponse
    {
        return (new InscripcionAyudantiaResource($this->inscripcionAyudantiaService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): InscripcionAyudantiaResource
    {
        return new InscripcionAyudantiaResource($this->inscripcionAyudantiaService->findOrFail($id));
    }

    public function update(UpdateInscripcionAyudantiaRequest $request, string $id): InscripcionAyudantiaResource
    {
        return new InscripcionAyudantiaResource($this->inscripcionAyudantiaService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->inscripcionAyudantiaService->delete($id);

        return response()->json(null, 204);
    }

    public function cancelar(string $id): InscripcionAyudantiaResource
    {
        return new InscripcionAyudantiaResource($this->inscripcionAyudantiaService->cancelar($id));
    }

    public function registrarAsistencia(RegistrarAsistenciaRequest $request, string $id): InscripcionAyudantiaResource
    {
        return new InscripcionAyudantiaResource(
            $this->inscripcionAyudantiaService->registrarAsistencia($id, (bool) $request->validated('asistencia'))
        );
    }
}
