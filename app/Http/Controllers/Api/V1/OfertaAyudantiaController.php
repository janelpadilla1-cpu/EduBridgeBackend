<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListOfertaAyudantiaRequest;
use App\Http\Requests\StoreOfertaAyudantiaRequest;
use App\Http\Requests\UpdateOfertaAyudantiaRequest;
use App\Http\Resources\OfertaAyudantiaResource;
use App\Services\OfertaAyudantiaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class OfertaAyudantiaController extends Controller
{
    public function __construct(
        private readonly OfertaAyudantiaService $ofertaAyudantiaService
    ) {}

    public function index(ListOfertaAyudantiaRequest $request): AnonymousResourceCollection
    {
        return OfertaAyudantiaResource::collection($this->ofertaAyudantiaService->paginate($request->validated()));
    }

    public function store(StoreOfertaAyudantiaRequest $request): JsonResponse
    {
        return (new OfertaAyudantiaResource($this->ofertaAyudantiaService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): OfertaAyudantiaResource
    {
        return new OfertaAyudantiaResource($this->ofertaAyudantiaService->findOrFail($id));
    }

    public function update(UpdateOfertaAyudantiaRequest $request, string $id): OfertaAyudantiaResource
    {
        return new OfertaAyudantiaResource($this->ofertaAyudantiaService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->ofertaAyudantiaService->delete($id);

        return response()->json(null, 204);
    }

    public function publicar(string $id): OfertaAyudantiaResource
    {
        return new OfertaAyudantiaResource($this->ofertaAyudantiaService->publicar($id));
    }

    public function cerrar(string $id): OfertaAyudantiaResource
    {
        return new OfertaAyudantiaResource($this->ofertaAyudantiaService->cerrar($id));
    }

    public function cancelar(string $id): OfertaAyudantiaResource
    {
        return new OfertaAyudantiaResource($this->ofertaAyudantiaService->cancelar($id));
    }
}
