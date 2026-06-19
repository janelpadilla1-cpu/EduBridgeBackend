<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListUsuarioRequest;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioService $usuarioService
    ) {}

    public function index(ListUsuarioRequest $request): AnonymousResourceCollection
    {
        return UsuarioResource::collection($this->usuarioService->paginate($request->validated()));
    }

    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        return (new UsuarioResource($this->usuarioService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): UsuarioResource
    {
        return new UsuarioResource($this->usuarioService->findOrFail($id));
    }

    public function update(UpdateUsuarioRequest $request, string $id): UsuarioResource
    {
        return new UsuarioResource($this->usuarioService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->usuarioService->delete($id);

        return response()->json(null, 204);
    }
}
