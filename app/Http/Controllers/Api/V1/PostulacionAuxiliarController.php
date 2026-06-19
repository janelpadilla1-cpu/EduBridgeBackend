<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListPostulacionAuxiliarRequest;
use App\Http\Requests\StorePostulacionAuxiliarRequest;
use App\Http\Requests\UpdatePostulacionAuxiliarRequest;
use App\Http\Resources\PostulacionAuxiliarResource;
use App\Services\PostulacionAuxiliarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class PostulacionAuxiliarController extends Controller
{
    public function __construct(
        private readonly PostulacionAuxiliarService $postulacionAuxiliarService
    ) {}

    public function index(ListPostulacionAuxiliarRequest $request): AnonymousResourceCollection
    {
        return PostulacionAuxiliarResource::collection($this->postulacionAuxiliarService->paginate($request->validated()));
    }

    public function store(StorePostulacionAuxiliarRequest $request): JsonResponse
    {
        return (new PostulacionAuxiliarResource($this->postulacionAuxiliarService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): PostulacionAuxiliarResource
    {
        return new PostulacionAuxiliarResource($this->postulacionAuxiliarService->findOrFail($id));
    }

    public function update(UpdatePostulacionAuxiliarRequest $request, string $id): PostulacionAuxiliarResource
    {
        return new PostulacionAuxiliarResource($this->postulacionAuxiliarService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->postulacionAuxiliarService->delete($id);

        return response()->json(null, 204);
    }

    public function aprobar(string $id): PostulacionAuxiliarResource
    {
        return new PostulacionAuxiliarResource($this->postulacionAuxiliarService->aprobar($id));
    }

    public function rechazar(string $id): PostulacionAuxiliarResource
    {
        return new PostulacionAuxiliarResource($this->postulacionAuxiliarService->rechazar($id));
    }

    public function cancelar(string $id): PostulacionAuxiliarResource
    {
        return new PostulacionAuxiliarResource($this->postulacionAuxiliarService->cancelar($id));
    }
}
