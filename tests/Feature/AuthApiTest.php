<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_local_account(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'correo_institucional' => 'estudiante.demo@edu.bo',
            'nombre_completo' => 'Estudiante Demo Interno',
            'rol' => 'ESTUDIANTE',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['token_type', 'access_token', 'usuario' => ['id', 'correo_institucional', 'nombre_completo', 'roles']]);
    }
}
