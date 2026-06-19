<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRolUsuarioRequest;
use App\Http\Requests\StoreRolUsuarioRequest;
use App\Http\Requests\UpdateRolUsuarioRequest;
use App\Http\Resources\RolUsuarioResource;
use App\Services\RolUsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class RolUsuarioController extends Controller
{
    public function __construct(
        private readonly RolUsuarioService $rolUsuarioService
    ) {}

    public function index(ListRolUsuarioRequest $request): AnonymousResourceCollection
    {
        return RolUsuarioResource::collection($this->rolUsuarioService->paginate($request->validated()));
    }

    public function store(StoreRolUsuarioRequest $request): JsonResponse
    {
        return (new RolUsuarioResource($this->rolUsuarioService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): RolUsuarioResource
    {
        return new RolUsuarioResource($this->rolUsuarioService->findOrFail($id));
    }

    public function update(UpdateRolUsuarioRequest $request, string $id): RolUsuarioResource
    {
        return new RolUsuarioResource($this->rolUsuarioService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->rolUsuarioService->delete($id);

        return response()->json(null, 204);
    }
}
