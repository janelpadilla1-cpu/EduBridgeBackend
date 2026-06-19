<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UsuarioResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->crearCuenta($request->validated());

        return response()->json([
            'token_type' => $result['token_type'],
            'access_token' => $result['access_token'],
            'usuario' => new UsuarioResource($result['usuario']),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->iniciarSesion($request->validated());

        return response()->json([
            'token_type' => $result['token_type'],
            'access_token' => $result['access_token'],
            'usuario' => new UsuarioResource($result['usuario']),
        ]);
    }

    public function me(Request $request): UsuarioResource
    {
        return new UsuarioResource($request->user()->load('roles'));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->cerrarSesion($request->user());

        return response()->json(null, 204);
    }
}
