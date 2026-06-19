<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MateriaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'codigo' => fake()->unique()->bothify('MAT-###'),
            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'estado' => 'ACTIVA',
        ];
    }
}
