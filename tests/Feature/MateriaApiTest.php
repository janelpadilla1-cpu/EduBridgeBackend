<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MateriaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_materia(): void
    {
        Sanctum::actingAs(Usuario::factory()->create());

        $response = $this->postJson('/api/v1/materias', [
            'codigo' => 'SIS-101',
            'nombre' => 'Programación I',
            'descripcion' => 'Materia base de programación.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.codigo', 'SIS-101');
    }
}
