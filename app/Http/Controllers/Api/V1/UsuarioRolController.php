<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListUsuarioRolRequest;
use App\Http\Requests\StoreUsuarioRolRequest;
use App\Http\Requests\UpdateUsuarioRolRequest;
use App\Http\Resources\UsuarioRolResource;
use App\Services\UsuarioRolService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class UsuarioRolController extends Controller
{
    public function __construct(
        private readonly UsuarioRolService $usuarioRolService
    ) {}

    public function index(ListUsuarioRolRequest $request): AnonymousResourceCollection
    {
        return UsuarioRolResource::collection($this->usuarioRolService->paginate($request->validated()));
    }

    public function store(StoreUsuarioRolRequest $request): JsonResponse
    {
        return (new UsuarioRolResource($this->usuarioRolService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): UsuarioRolResource
    {
        return new UsuarioRolResource($this->usuarioRolService->findOrFail($id));
    }

    public function update(UpdateUsuarioRolRequest $request, string $id): UsuarioRolResource
    {
        return new UsuarioRolResource($this->usuarioRolService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->usuarioRolService->delete($id);

        return response()->json(null, 204);
    }
}
