# Tests

Pruebas Feature para validar la API.

## Ejecutar todos los tests

```bash
php artisan test
```

## Ejecutar solo smoke test

```bash
composer test:smoke
```

Equivale a:

```bash
php artisan test --filter=SmokeApiTest
```

## Qué valida el smoke test

- `GET /api/v1/catalogos/estados`
- `POST /api/v1/auth/register`
- Token Sanctum.
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/login`
- `POST /api/v1/materias`

## Cuidado

Los tests usan `RefreshDatabase`. Ejecútalos en entorno de pruebas, no contra una base con datos reales.


## Registro interno

El body de registro usado por los smoke tests incluye:

```json
{
  "correo_institucional": "smoke@example.edu.bo",
  "nombre_completo": "Usuario Smoke Test",
  "rol": "ESTUDIANTE",
  "password": "Password123!",
  "password_confirmation": "Password123!"
}
```

Roles permitidos: `ESTUDIANTE`, `AUXILIAR`, `COORDINADOR`, `ADMINISTRADOR`.
