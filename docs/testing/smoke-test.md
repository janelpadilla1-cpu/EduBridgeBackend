# Smoke test completo

El smoke test ya no valida solo login y una materia. Ahora cubre el flujo completo de la API con payloads reales.

## Qué valida

1. Catálogos públicos.
2. Registro interno con rol.
3. Login y token Sanctum.
4. `/auth/me` y `/auth/logout`.
5. CRUD completo de:
   - usuarios
   - cuentas de usuario
   - roles
   - usuarios/roles
   - materias
   - ofertas de ayudantía
   - sesiones de ayudantía
   - inscripciones
   - postulaciones
   - auxiliares por materia
   - disponibilidad auxiliar
6. Acciones de negocio:
   - publicar, cerrar y cancelar ofertas
   - iniciar, finalizar y cancelar sesiones
   - cancelar inscripción
   - registrar asistencia
   - aprobar, rechazar y cancelar postulaciones
7. `DELETE` de recursos al final del flujo.

## Antes de ejecutar

Levanta la API:

```bash
php artisan serve
```

URL esperada:

```txt
http://127.0.0.1:8000
```

## PowerShell en Windows

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\smoke-test.ps1
```

Con URL personalizada:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\smoke-test.ps1 -BaseUrl "http://localhost:8000"
```

## PHPUnit

```bash
composer test:smoke
```

El test usa `RefreshDatabase`. No lo ejecutes contra una base con datos que quieras conservar.

## Postman

Importa:

```txt
docs/postman/smoke-test.collection.json
docs/postman/EduBridgeBackend.local.environment.json
```

Luego ejecuta la colección completa desde Collection Runner.

## Importante sobre los datos

El smoke genera correos, códigos y referencias únicas por corrida usando un `run_id`. Eso evita choques con campos únicos como:

- `usuarios.external_user_ref`
- `usuarios.correo_institucional`
- `usuarios.codigo_universitario`
- `materias.codigo`
- `roles_usuario.nombre`

## Resultado esperado

PowerShell debe terminar con:

```txt
SMOKE TEST COMPLETO OK
```

Postman debe mostrar todos los requests en verde.

PHPUnit debe terminar con `OK`.
