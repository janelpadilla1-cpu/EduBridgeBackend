<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SmokeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_full_smoke_flow_covers_all_routes_with_real_payloads(): void
    {
        $suffix = (string) str_replace('.', '', (string) microtime(true));
        $password = 'Password123!';

        $this->getJson('/api/v1/catalogos/estados')
            ->assertOk()
            ->assertJsonStructure([
                'usuario',
                'cuenta_usuario',
                'materia',
                'oferta_ayudantia',
                'sesion_ayudantia',
                'inscripcion_ayudantia',
                'postulacion_auxiliar',
                'auxiliar_materia',
                'disponibilidad_auxiliar',
            ]);

        $register = $this->postJson('/api/v1/auth/register', [
            'correo_institucional' => "admin.{$suffix}@edu.bo",
            'nombre_completo' => 'Administrador Smoke Test',
            'rol' => 'ADMINISTRADOR',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertCreated()
            ->assertJsonStructure(['token_type', 'access_token', 'usuario' => ['id', 'correo_institucional', 'roles']]);

        $token = $register->json('access_token');
        $usuarioId = $register->json('usuario.id');

        $this->withToken($token)->getJson('/api/v1/auth/me')->assertOk();

        $login = $this->postJson('/api/v1/auth/login', [
            'correo_institucional' => "admin.{$suffix}@edu.bo",
            'password' => $password,
        ])->assertOk()->assertJsonStructure(['token_type', 'access_token', 'usuario' => ['id']]);

        $token = $login->json('access_token');

        $this->withToken($token)->getJson('/api/v1/usuarios?per_page=5&sort_by=created_at&sort_order=desc')->assertOk();
        $createdUser = $this->withToken($token)->postJson('/api/v1/usuarios', [
            'external_user_ref' => "local-estudiante-{$suffix}",
            'codigo_universitario' => "EST-{$suffix}",
            'correo_institucional' => "estudiante.{$suffix}@edu.bo",
            'nombre_completo' => 'Estudiante CRUD Smoke',
            'estado' => 'ACTIVO',
        ])->assertCreated();
        $createdUserId = $this->dataId($createdUser);
        $this->withToken($token)->getJson("/api/v1/usuarios/{$createdUserId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/usuarios/{$createdUserId}", [
            'nombre_completo' => 'Estudiante CRUD Smoke PATCH',
            'estado' => 'ACTIVO',
        ])->assertOk();
        $this->withToken($token)->putJson("/api/v1/usuarios/{$createdUserId}", [
            'external_user_ref' => "local-estudiante-{$suffix}",
            'codigo_universitario' => "EST-{$suffix}",
            'correo_institucional' => "estudiante.{$suffix}@edu.bo",
            'nombre_completo' => 'Estudiante CRUD Smoke PUT',
            'estado' => 'ACTIVO',
        ])->assertOk();

        $this->withToken($token)->getJson('/api/v1/cuentas-usuario?per_page=5&sort_by=created_at&sort_order=desc')->assertOk();
        $cuenta = $this->withToken($token)->postJson('/api/v1/cuentas-usuario', [
            'usuario_id' => $createdUserId,
            'password_hash' => '$2y$12$Hc6gP0zZQzQkgjZ6bgGgku6e1ztuACNQdA0nhx1V1i7iFNeJ3JiU2',
            'estado' => 'ACTIVA',
            'ultimo_acceso' => '2030-01-01 08:00:00',
        ])->assertCreated();
        $cuentaId = $this->dataId($cuenta);
        $this->withToken($token)->getJson("/api/v1/cuentas-usuario/{$cuentaId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/cuentas-usuario/{$cuentaId}", [
            'estado' => 'BLOQUEADA',
            'ultimo_acceso' => '2030-01-02 09:00:00',
        ])->assertOk();
        $this->withToken($token)->putJson("/api/v1/cuentas-usuario/{$cuentaId}", [
            'usuario_id' => $createdUserId,
            'password_hash' => '$2y$12$Hc6gP0zZQzQkgjZ6bgGgku6e1ztuACNQdA0nhx1V1i7iFNeJ3JiU2',
            'estado' => 'ACTIVA',
            'ultimo_acceso' => '2030-01-03 10:00:00',
        ])->assertOk();

        $this->withToken($token)->getJson('/api/v1/roles-usuario?per_page=10&sort_by=created_at&sort_order=desc')->assertOk();
        $rol = $this->withToken($token)->postJson('/api/v1/roles-usuario', [
            'nombre' => "MONITOR_SMOKE_{$suffix}",
            'descripcion' => 'Rol temporal para smoke test completo.',
        ])->assertCreated();
        $rolId = $this->dataId($rol);
        $this->withToken($token)->getJson("/api/v1/roles-usuario/{$rolId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/roles-usuario/{$rolId}", ['descripcion' => 'Rol actualizado por PATCH.'])->assertOk();
        $this->withToken($token)->putJson("/api/v1/roles-usuario/{$rolId}", [
            'nombre' => "MONITOR_SMOKE_{$suffix}",
            'descripcion' => 'Rol actualizado por PUT.',
        ])->assertOk();

        $this->withToken($token)->getJson('/api/v1/usuarios-roles?per_page=5&sort_by=created_at&sort_order=desc')->assertOk();
        $usuarioRol = $this->withToken($token)->postJson('/api/v1/usuarios-roles', [
            'usuario_id' => $createdUserId,
            'rol_id' => $rolId,
        ])->assertCreated();
        $usuarioRolId = $this->dataId($usuarioRol);
        $this->withToken($token)->getJson("/api/v1/usuarios-roles/{$usuarioRolId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/usuarios-roles/{$usuarioRolId}", ['usuario_id' => $createdUserId, 'rol_id' => $rolId])->assertOk();
        $this->withToken($token)->putJson("/api/v1/usuarios-roles/{$usuarioRolId}", ['usuario_id' => $createdUserId, 'rol_id' => $rolId])->assertOk();

        $this->withToken($token)->getJson('/api/v1/materias?per_page=10&sort_by=created_at&sort_order=desc')->assertOk();
        $materiaId = $this->crearMateria($token, "MAT-{$suffix}", 'Contabilidad I Smoke');
        $materiaAprobarId = $this->crearMateria($token, "MAT-APR-{$suffix}", 'Costos Smoke');
        $materiaRechazarId = $this->crearMateria($token, "MAT-REJ-{$suffix}", 'Auditoría Smoke');
        $materiaCancelarId = $this->crearMateria($token, "MAT-CAN-{$suffix}", 'Tributaria Smoke');
        $materiaAuxManualId = $this->crearMateria($token, "MAT-AUX-{$suffix}", 'Finanzas Smoke');
        $materiaDeleteId = $this->crearMateria($token, "MAT-DEL-{$suffix}", 'Materia Delete Smoke');

        $this->withToken($token)->getJson("/api/v1/materias/{$materiaId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/materias/{$materiaId}", [
            'nombre' => 'Contabilidad I Smoke PATCH',
            'descripcion' => 'Descripción actualizada por PATCH.',
            'estado' => 'ACTIVA',
        ])->assertOk();
        $this->withToken($token)->putJson("/api/v1/materias/{$materiaId}", [
            'codigo' => "MAT-{$suffix}",
            'nombre' => 'Contabilidad I Smoke PUT',
            'descripcion' => 'Descripción actualizada por PUT.',
            'estado' => 'ACTIVA',
        ])->assertOk();

        $this->withToken($token)->getJson('/api/v1/ofertas-ayudantia?per_page=10&sort_by=created_at&sort_order=desc')->assertOk();
        $ofertaId = $this->crearOferta($token, $materiaId, 'Oferta principal Smoke', 6);
        $ofertaCancelId = $this->crearOferta($token, $materiaId, 'Oferta cancelable Smoke', 3);
        $ofertaDeleteId = $this->crearOferta($token, $materiaDeleteId, 'Oferta delete Smoke', 1);
        $this->withToken($token)->getJson("/api/v1/ofertas-ayudantia/{$ofertaId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/ofertas-ayudantia/{$ofertaId}", [
            'titulo' => 'Oferta principal PATCH',
            'descripcion' => 'Patch real de oferta.',
            'cupo_maximo' => 7,
            'estado' => 'BORRADOR',
        ])->assertOk();
        $this->withToken($token)->putJson("/api/v1/ofertas-ayudantia/{$ofertaId}", [
            'materia_id' => $materiaId,
            'titulo' => 'Oferta principal PUT',
            'descripcion' => 'Put real de oferta.',
            'cupo_maximo' => 7,
            'estado' => 'BORRADOR',
        ])->assertOk();
        $this->withToken($token)->postJson("/api/v1/ofertas-ayudantia/{$ofertaId}/publicar", ['motivo_operacion' => 'Smoke publicar'])->assertOk();
        $this->withToken($token)->postJson("/api/v1/ofertas-ayudantia/{$ofertaId}/cerrar", ['motivo_operacion' => 'Smoke cerrar'])->assertOk();
        $this->withToken($token)->postJson("/api/v1/ofertas-ayudantia/{$ofertaCancelId}/cancelar", ['motivo_operacion' => 'Smoke cancelar'])->assertOk();

        $this->withToken($token)->getJson('/api/v1/sesiones-ayudantia?per_page=10&sort_by=created_at&sort_order=desc')->assertOk();
        $sesionId = $this->crearSesion($token, $ofertaId, $usuarioId, "AULA-101-{$suffix}", '08:00', '09:00');
        $sesionCancelId = $this->crearSesion($token, $ofertaId, $usuarioId, "AULA-102-{$suffix}", '09:15', '10:15');
        $sesionDeleteId = $this->crearSesion($token, $ofertaId, $usuarioId, "AULA-103-{$suffix}", '10:30', '11:30');
        $this->withToken($token)->getJson("/api/v1/sesiones-ayudantia/{$sesionId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/sesiones-ayudantia/{$sesionId}", [
            'aula_nombre_cache' => 'Aula 101 PATCH',
            'estado' => 'PROGRAMADA',
        ])->assertOk();
        $this->withToken($token)->putJson("/api/v1/sesiones-ayudantia/{$sesionId}", [
            'oferta_ayudantia_id' => $ofertaId,
            'auxiliar_id' => $usuarioId,
            'fecha' => '2030-08-15',
            'hora_inicio' => '08:00',
            'hora_fin' => '09:00',
            'aula_ref_id' => "AULA-101-{$suffix}",
            'aula_nombre_cache' => 'Aula 101 PUT',
            'estado' => 'PROGRAMADA',
        ])->assertOk();

        $this->withToken($token)->getJson('/api/v1/inscripciones-ayudantia?per_page=10&sort_by=created_at&sort_order=desc')->assertOk();
        $inscripcionId = $this->crearInscripcion($token, $usuarioId, $sesionId);
        $inscripcionCancelId = $this->crearInscripcion($token, $createdUserId, $sesionCancelId);
        $inscripcionDeleteId = $this->crearInscripcion($token, $createdUserId, $sesionDeleteId);
        $this->withToken($token)->getJson("/api/v1/inscripciones-ayudantia/{$inscripcionId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/inscripciones-ayudantia/{$inscripcionId}", [
            'usuario_id' => $usuarioId,
            'sesion_ayudantia_id' => $sesionId,
        ])->assertOk();
        $this->withToken($token)->putJson("/api/v1/inscripciones-ayudantia/{$inscripcionId}", [
            'usuario_id' => $usuarioId,
            'sesion_ayudantia_id' => $sesionId,
        ])->assertOk();
        $this->withToken($token)->patchJson("/api/v1/inscripciones-ayudantia/{$inscripcionId}/asistencia", ['asistencia' => true])->assertOk();
        $this->withToken($token)->postJson("/api/v1/inscripciones-ayudantia/{$inscripcionCancelId}/cancelar", ['motivo_operacion' => 'Smoke cancelar inscripción'])->assertOk();

        $this->withToken($token)->postJson("/api/v1/sesiones-ayudantia/{$sesionId}/iniciar", ['motivo_operacion' => 'Smoke iniciar'])->assertOk();
        $this->withToken($token)->postJson("/api/v1/sesiones-ayudantia/{$sesionId}/finalizar", ['motivo_operacion' => 'Smoke finalizar'])->assertOk();
        $this->withToken($token)->postJson("/api/v1/sesiones-ayudantia/{$sesionCancelId}/cancelar", ['motivo_operacion' => 'Smoke cancelar sesión'])->assertOk();

        $this->withToken($token)->getJson('/api/v1/postulaciones-auxiliar?per_page=10&sort_by=created_at&sort_order=desc')->assertOk();
        $postulacionId = $this->crearPostulacion($token, $usuarioId, $materiaAprobarId, 'Motivo para aprobar');
        $postulacionRechazarId = $this->crearPostulacion($token, $usuarioId, $materiaRechazarId, 'Motivo para rechazar');
        $postulacionCancelarId = $this->crearPostulacion($token, $usuarioId, $materiaCancelarId, 'Motivo para cancelar');
        $postulacionDeleteId = $this->crearPostulacion($token, $createdUserId, $materiaDeleteId, 'Motivo para delete');
        $this->withToken($token)->getJson("/api/v1/postulaciones-auxiliar/{$postulacionId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/postulaciones-auxiliar/{$postulacionId}", [
            'motivo' => 'Motivo PATCH',
            'experiencia' => 'Experiencia PATCH',
        ])->assertOk();
        $this->withToken($token)->putJson("/api/v1/postulaciones-auxiliar/{$postulacionId}", [
            'usuario_id' => $usuarioId,
            'materia_id' => $materiaAprobarId,
            'motivo' => 'Motivo PUT',
            'experiencia' => 'Experiencia PUT',
        ])->assertOk();
        $this->withToken($token)->postJson("/api/v1/postulaciones-auxiliar/{$postulacionId}/aprobar", ['motivo_operacion' => 'Smoke aprobar'])->assertOk();
        $this->withToken($token)->postJson("/api/v1/postulaciones-auxiliar/{$postulacionRechazarId}/rechazar", ['motivo_operacion' => 'Smoke rechazar'])->assertOk();
        $this->withToken($token)->postJson("/api/v1/postulaciones-auxiliar/{$postulacionCancelarId}/cancelar", ['motivo_operacion' => 'Smoke cancelar postulación'])->assertOk();

        $this->withToken($token)->getJson('/api/v1/auxiliares-materia?per_page=100&sort_by=created_at&sort_order=desc')->assertOk();
        $auxiliarMateria = $this->withToken($token)->postJson('/api/v1/auxiliares-materia', [
            'usuario_id' => $createdUserId,
            'materia_id' => $materiaAuxManualId,
            'estado' => 'ACTIVO',
        ])->assertCreated();
        $auxiliarMateriaId = $this->dataId($auxiliarMateria);
        $this->withToken($token)->getJson("/api/v1/auxiliares-materia/{$auxiliarMateriaId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/auxiliares-materia/{$auxiliarMateriaId}", ['estado' => 'INACTIVO'])->assertOk();
        $this->withToken($token)->putJson("/api/v1/auxiliares-materia/{$auxiliarMateriaId}", [
            'usuario_id' => $createdUserId,
            'materia_id' => $materiaAuxManualId,
            'estado' => 'ACTIVO',
        ])->assertOk();

        $this->withToken($token)->getJson('/api/v1/disponibilidad-auxiliar?per_page=10&sort_by=created_at&sort_order=desc')->assertOk();
        $disponibilidad = $this->withToken($token)->postJson('/api/v1/disponibilidad-auxiliar', [
            'usuario_id' => $usuarioId,
            'dia_semana' => 'LUNES',
            'hora_inicio' => '14:00',
            'hora_fin' => '16:00',
            'estado' => 'ACTIVA',
        ])->assertCreated();
        $disponibilidadId = $this->dataId($disponibilidad);
        $this->withToken($token)->getJson("/api/v1/disponibilidad-auxiliar/{$disponibilidadId}")->assertOk();
        $this->withToken($token)->patchJson("/api/v1/disponibilidad-auxiliar/{$disponibilidadId}", ['estado' => 'INACTIVA'])->assertOk();
        $this->withToken($token)->putJson("/api/v1/disponibilidad-auxiliar/{$disponibilidadId}", [
            'usuario_id' => $usuarioId,
            'dia_semana' => 'LUNES',
            'hora_inicio' => '14:00',
            'hora_fin' => '16:00',
            'estado' => 'ACTIVA',
        ])->assertOk();

        $this->withToken($token)->deleteJson("/api/v1/disponibilidad-auxiliar/{$disponibilidadId}")->assertNoContent();
        $this->withToken($token)->deleteJson("/api/v1/auxiliares-materia/{$auxiliarMateriaId}")->assertNoContent();
        $this->withToken($token)->deleteJson("/api/v1/postulaciones-auxiliar/{$postulacionDeleteId}")->assertNoContent();
        $this->withToken($token)->deleteJson("/api/v1/inscripciones-ayudantia/{$inscripcionDeleteId}")->assertNoContent();
        $this->withToken($token)->deleteJson("/api/v1/sesiones-ayudantia/{$sesionDeleteId}")->assertNoContent();
        $this->withToken($token)->deleteJson("/api/v1/ofertas-ayudantia/{$ofertaDeleteId}")->assertNoContent();
        $this->withToken($token)->deleteJson("/api/v1/materias/{$materiaDeleteId}")->assertNoContent();
        $this->withToken($token)->deleteJson("/api/v1/usuarios-roles/{$usuarioRolId}")->assertNoContent();
        $this->withToken($token)->deleteJson("/api/v1/roles-usuario/{$rolId}")->assertNoContent();
        $this->withToken($token)->deleteJson("/api/v1/cuentas-usuario/{$cuentaId}")->assertNoContent();
        $this->withToken($token)->deleteJson("/api/v1/usuarios/{$createdUserId}")->assertNoContent();
        $this->withToken($token)->postJson('/api/v1/auth/logout', ['motivo_operacion' => 'Smoke logout'])->assertNoContent();
    }

    private function dataId(TestResponse $response): string
    {
        $id = $response->json('data.id') ?? $response->json('id');
        $this->assertIsString($id);
        $this->assertNotSame('', $id);

        return $id;
    }

    private function crearMateria(string $token, string $codigo, string $nombre): string
    {
        return $this->dataId($this->withToken($token)->postJson('/api/v1/materias', [
            'codigo' => $codigo,
            'nombre' => $nombre,
            'descripcion' => "Descripción de {$nombre}.",
            'estado' => 'ACTIVA',
        ])->assertCreated());
    }

    private function crearOferta(string $token, string $materiaId, string $titulo, int $cupo): string
    {
        return $this->dataId($this->withToken($token)->postJson('/api/v1/ofertas-ayudantia', [
            'materia_id' => $materiaId,
            'titulo' => $titulo,
            'descripcion' => "Descripción de {$titulo}.",
            'cupo_maximo' => $cupo,
            'estado' => 'BORRADOR',
        ])->assertCreated());
    }

    private function crearSesion(string $token, string $ofertaId, string $auxiliarId, string $aula, string $inicio, string $fin): string
    {
        return $this->dataId($this->withToken($token)->postJson('/api/v1/sesiones-ayudantia', [
            'oferta_ayudantia_id' => $ofertaId,
            'auxiliar_id' => $auxiliarId,
            'fecha' => '2030-08-15',
            'hora_inicio' => $inicio,
            'hora_fin' => $fin,
            'aula_ref_id' => $aula,
            'aula_nombre_cache' => "Aula {$aula}",
            'estado' => 'PROGRAMADA',
        ])->assertCreated());
    }

    private function crearInscripcion(string $token, string $usuarioId, string $sesionId): string
    {
        return $this->dataId($this->withToken($token)->postJson('/api/v1/inscripciones-ayudantia', [
            'usuario_id' => $usuarioId,
            'sesion_ayudantia_id' => $sesionId,
        ])->assertCreated());
    }

    private function crearPostulacion(string $token, string $usuarioId, string $materiaId, string $motivo): string
    {
        return $this->dataId($this->withToken($token)->postJson('/api/v1/postulaciones-auxiliar', [
            'usuario_id' => $usuarioId,
            'materia_id' => $materiaId,
            'motivo' => $motivo,
            'experiencia' => 'Experiencia realista de prueba para smoke test.',
        ])->assertCreated());
    }
}
