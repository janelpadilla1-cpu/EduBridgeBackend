<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'external_user_ref' => fake()->unique()->uuid(),
            'codigo_universitario' => fake()->unique()->numerify('UNI#####'),
            'correo_institucional' => fake()->unique()->safeEmail(),
            'nombre_completo' => fake()->name(),
            'estado' => 'ACTIVO',
            'fecha_registro' => now(),
        ];
    }
}
