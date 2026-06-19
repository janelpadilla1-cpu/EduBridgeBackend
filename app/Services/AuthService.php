<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Gateways\Directorio\DirectorioUniversitarioGatewayInterface;
use App\Models\CuentaUsuario;
use App\Models\RolUsuario;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly DirectorioUniversitarioGatewayInterface $directorioGateway
    ) {}

    public function crearCuenta(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $datosExternos = $this->directorioGateway->validarUsuarioUniversitario(
                $data['external_user_ref'],
                $data['correo_institucional'] ?? null,
            );

            $usuario = Usuario::query()->firstOrCreate(
                ['external_user_ref' => $datosExternos['external_user_ref']],
                [
                    'codigo_universitario' => $datosExternos['codigo_universitario'],
                    'correo_institucional' => $datosExternos['correo_institucional'],
                    'nombre_completo' => $datosExternos['nombre_completo'],
                    'estado' => 'ACTIVO',
                    'fecha_registro' => now(),
                ]
            );

            if ($usuario->cuenta()->exists()) {
                throw new BusinessRuleException('El usuario ya tiene una cuenta local creada.');
            }

            CuentaUsuario::query()->create([
                'usuario_id' => $usuario->id,
                'password_hash' => Hash::make($data['password']),
                'estado' => 'ACTIVA',
            ]);

            $rolEstudiante = RolUsuario::query()->firstOrCreate(
                ['nombre' => 'ESTUDIANTE'],
                ['descripcion' => 'Usuario que puede inscribirse a sesiones de ayudantía']
            );

            $usuario->roles()->syncWithoutDetaching([$rolEstudiante->id]);

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
}
