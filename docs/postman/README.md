# Postman

Esta carpeta contiene las colecciones corregidas para probar EduBridgeBackend con el contrato actual de **registro interno**.

## Archivos

| Archivo | Uso recomendado |
|---|---|
| `collection.json` | Colección completa ejecutable: cubre todas las rutas reales de `routes/api.php`. |
| `smoke-test.collection.json` | Misma lógica, preparada para Collection Runner como smoke test completo. |
| `EduBridgeBackend.local.environment.json` | Ambiente local mínimo con `base_url`. |

## Configuración obligatoria

En el environment usa:

```txt
base_url = http://127.0.0.1:8000
```

No uses:

```txt
base_url = http://127.0.0.1:8000/api
```

La colección ya incluye `/api/v1` en cada request. Si agregas `/api` en `base_url`, Postman llamará rutas duplicadas como `/api/api/v1/...` y aparecerán errores `404 Not Found`.

## Qué se corrigió

La colección fue rearmada como flujo ejecutable completo:

1. Inicializa variables únicas por corrida.
2. Registra un usuario `ADMINISTRADOR` internamente.
3. Guarda `access_token` automáticamente.
4. Crea datos base con payloads reales, no vacíos.
5. Prueba CRUD de todos los recursos.
6. Prueba acciones de negocio.
7. Ejecuta `DELETE` al final para no romper dependencias.
8. Hace logout.

## Cobertura

La colección cubre las **82 rutas únicas** generadas por:

- Auth público y protegido.
- Catálogos.
- CRUD completo de 11 recursos.
- Acciones de negocio de ofertas, sesiones, inscripciones y postulaciones.

Como algunos endpoints necesitan datos distintos para aprobar, rechazar, cancelar o eliminar, la colección tiene más requests que rutas únicas. Eso es intencional.

## Orden correcto de ejecución

Ejecuta la colección desde el **Collection Runner** de arriba hacia abajo.

No ejecutes primero los folders de `DELETE`, porque eliminan datos que los módulos anteriores usan.

## Payloads reales

Todos los `POST`, `PUT` y `PATCH` de recursos CRUD tienen bodies reales. Ejemplo de registro interno:

```json
{
  "correo_institucional": "admin.{{run_id}}@edu.bo",
  "nombre_completo": "Administrador Postman {{run_id}}",
  "rol": "ADMINISTRADOR",
  "password": "Password123!",
  "password_confirmation": "Password123!"
}
```

Ejemplo de materia:

```json
{
  "codigo": "MAT-{{run_id}}",
  "nombre": "Contabilidad I {{run_id}}",
  "descripcion": "Materia principal para flujo completo de ayudantías",
  "estado": "ACTIVA"
}
```

## Variables principales

| Variable | Uso |
|---|---|
| `run_id` | Sufijo único por corrida. Evita choques de datos únicos. |
| `access_token` | Token Bearer de Sanctum. Se guarda automáticamente. |
| `user_id` | Usuario autenticado creado por register. |
| `created_user_id` | Usuario adicional creado por CRUD. |
| `materia_id` | Materia principal. |
| `oferta_id` | Oferta principal. |
| `sesion_id` | Sesión principal. |
| `inscripcion_id` | Inscripción principal. |
| `postulacion_id` | Postulación usada para aprobar. |

## Errores comunes

| Error | Causa | Solución |
|---|---|---|
| `404 Not Found` | `base_url` incluye `/api` o se ejecuta una colección vieja | Usa esta colección y `base_url=http://127.0.0.1:8000` |
| `401 Unauthorized` | No se ejecutó register/login antes de rutas protegidas | Ejecuta desde el primer request |
| `422 Unprocessable Entity` | Corrida repetida con variables viejas o payload alterado | Vuelve a iniciar desde `00.01 Init run + Catálogos estados` |
| `500` por claves foráneas | Se ejecutó un DELETE antes de tiempo | Ejecuta la colección completa en orden |

## Recomendación

Para validar rápido desde consola usa:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\smoke-test.ps1
```

Para validar desde PHPUnit usa:

```bash
composer test:smoke
```
