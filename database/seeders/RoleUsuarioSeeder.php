<?php

namespace Database\Seeders;

use App\Models\RolUsuario;
use Illuminate\Database\Seeder;

class RoleUsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'ESTUDIANTE', 'descripcion' => 'Usuario que puede inscribirse a sesiones de ayudantía'],
            ['nombre' => 'AUXILIAR', 'descripcion' => 'Usuario que puede dictar sesiones de ayudantía'],
            ['nombre' => 'COORDINADOR', 'descripcion' => 'Usuario que administra ofertas, sesiones y postulaciones'],
            ['nombre' => 'ADMINISTRADOR', 'descripcion' => 'Usuario con permisos generales del sistema'],
        ];

        foreach ($roles as $rol) {
            RolUsuario::query()->firstOrCreate(['nombre' => $rol['nombre']], $rol);
        }
    }
}
