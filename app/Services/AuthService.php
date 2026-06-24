<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\CuentaUsuario;
use App\Models\RolUsuario;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function crearCuenta(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $rolNombre = Str::upper((string) ($data['rol'] ?? 'ESTUDIANTE'));
            $rolesPermitidos = ['ESTUDIANTE', 'AUXILIAR', 'COORDINADOR', 'ADMINISTRADOR'];

            if (! in_array($rolNombre, $rolesPermitidos, true)) {
                throw new BusinessRuleException('Rol de usuario no permitido.', 422);
            }

            $usuario = Usuario::query()->create([
                'external_user_ref' => $data['external_user_ref'] ?? $data['correo_institucional'],
                'codigo_universitario' => $data['codigo_universitario'] ?? null,
                'correo_institucional' => $data['correo_institucional'],
                'nombre_completo' => $data['nombre_completo'],
                'estado' => 'ACTIVO',
                'fecha_registro' => now(),
            ]);

            CuentaUsuario::query()->create([
                'usuario_id' => $usuario->id,
                'password_hash' => Hash::make($data['password']),
                'estado' => 'ACTIVA',
            ]);

            $rol = RolUsuario::query()->firstOrCreate(
                ['nombre' => $rolNombre],
                ['descripcion' => $this->descripcionRol($rolNombre)]
            );

            $usuario->roles()->syncWithoutDetaching([
                $rol->id => ['id' => (string) Str::uuid()],
            ]);

            return $this->emitirToken($usuario->refresh());
        });
    }

    public function iniciarSesion(array $data): array
    {
        $usuario = Usuario::query()
            ->where('correo_institucional', $data['correo_institucional'])
            ->with('cuenta')
            ->first();

        if (! $usuario || ! $usuario->cuenta || ! Hash::check($data['password'], $usuario->cuenta->password_hash)) {
            throw new BusinessRuleException('Credenciales inválidas.', 401);
        }

        if ($usuario->estado !== 'ACTIVO' || $usuario->cuenta->estado !== 'ACTIVA') {
            throw new BusinessRuleException('La cuenta no se encuentra activa.', 403);
        }

        $usuario->cuenta()->update(['ultimo_acceso' => now()]);

        return $this->emitirToken($usuario->refresh());
    }

    public function cerrarSesion(Usuario $usuario): void
    {
        $usuario->currentAccessToken()?->delete();
    }

    private function emitirToken(Usuario $usuario): array
    {
        $token = $usuario->createToken('edubridge-api')->plainTextToken;

        return [
            'token_type' => 'Bearer',
            'access_token' => $token,
            'usuario' => $usuario->load('roles'),
        ];
    }

    private function descripcionRol(string $rolNombre): string
    {
        return match ($rolNombre) {
            'AUXILIAR' => 'Usuario que puede dictar sesiones de ayudantía',
            'COORDINADOR' => 'Usuario que administra ofertas, sesiones y postulaciones',
            'ADMINISTRADOR' => 'Usuario con permisos generales del sistema',
            default => 'Usuario que puede inscribirse a sesiones de ayudantía',
        };
    }
}
