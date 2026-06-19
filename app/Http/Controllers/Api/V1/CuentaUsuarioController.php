<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListCuentaUsuarioRequest;
use App\Http\Requests\StoreCuentaUsuarioRequest;
use App\Http\Requests\UpdateCuentaUsuarioRequest;
use App\Http\Resources\CuentaUsuarioResource;
use App\Services\CuentaUsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class CuentaUsuarioController extends Controller
{
    public function __construct(
        private readonly CuentaUsuarioService $cuentaUsuarioService
    ) {}

    public function index(ListCuentaUsuarioRequest $request): AnonymousResourceCollection
    {
        return CuentaUsuarioResource::collection($this->cuentaUsuarioService->paginate($request->validated()));
    }

    public function store(StoreCuentaUsuarioRequest $request): JsonResponse
    {
        return (new CuentaUsuarioResource($this->cuentaUsuarioService->create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): CuentaUsuarioResource
    {
        return new CuentaUsuarioResource($this->cuentaUsuarioService->findOrFail($id));
    }

    public function update(UpdateCuentaUsuarioRequest $request, string $id): CuentaUsuarioResource
    {
        return new CuentaUsuarioResource($this->cuentaUsuarioService->update($id, $request->validated()));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->cuentaUsuarioService->delete($id);

        return response()->json(null, 204);
    }
}
