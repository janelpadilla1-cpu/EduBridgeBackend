# Endpoints - EduBridgeBackend

Base local:

```txt
http://127.0.0.1:8000/api/v1
```

En Postman usa `base_url = http://127.0.0.1:8000`.

## 1. Endpoints públicos

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/catalogos/estados` | Devuelve estados permitidos por módulo |
| POST | `/auth/register` | Crea cuenta local y devuelve token Sanctum |
| POST | `/auth/login` | Inicia sesión y devuelve token Sanctum |


## Registro interno

`POST /auth/register` crea el usuario completamente en la base local. No llama a ningún directorio externo.

Body recomendado:

```json
{
  "correo_institucional": "estudiante.demo@edu.bo",
  "nombre_completo": "Estudiante Demo",
  "rol": "ESTUDIANTE",
  "password": "Password123!",
  "password_confirmation": "Password123!"
}
```

Campos opcionales:

| Campo | Uso |
|---|---|
| `external_user_ref` | Referencia técnica interna. Si no se envía, se usa el correo. |
| `codigo_universitario` | Código universitario local, único si se envía. |

Roles permitidos: `ESTUDIANTE`, `AUXILIAR`, `COORDINADOR`, `ADMINISTRADOR`.

## 2. Auth protegido

Requieren header:

```txt
Authorization: Bearer {{access_token}}
```

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/auth/me` | Devuelve el usuario autenticado |
| POST | `/auth/logout` | Elimina el token actual |

## 3. Recursos CRUD protegidos

Todos los recursos usan el mismo patrón REST:

| Método | Ruta | Acción |
|---|---|---|
| GET | `/{recurso}` | Listar con paginación |
| POST | `/{recurso}` | Crear |
| GET | `/{recurso}/{id}` | Ver detalle |
| PUT | `/{recurso}/{id}` | Reemplazar/actualizar |
| PATCH | `/{recurso}/{id}` | Actualizar parcialmente |
| DELETE | `/{recurso}/{id}` | Eliminar |

Recursos reales:

| Recurso | Ruta base |
|---|---|
| Usuarios | `/usuarios` |
| Cuentas de usuario | `/cuentas-usuario` |
| Roles | `/roles-usuario` |
| Relación usuario/rol | `/usuarios-roles` |
| Materias | `/materias` |
| Ofertas de ayudantía | `/ofertas-ayudantia` |
| Sesiones de ayudantía | `/sesiones-ayudantia` |
| Inscripciones | `/inscripciones-ayudantia` |
| Postulaciones a auxiliar | `/postulaciones-auxiliar` |
| Auxiliares por materia | `/auxiliares-materia` |
| Disponibilidad auxiliar | `/disponibilidad-auxiliar` |

## 4. Acciones de negocio protegidas

| Método | Ruta | Regla general |
|---|---|---|
| POST | `/ofertas-ayudantia/{id}/publicar` | Cambia oferta de `BORRADOR` a `PUBLICADA` |
| POST | `/ofertas-ayudantia/{id}/cerrar` | Cambia oferta de `PUBLICADA` a `CERRADA` |
| POST | `/ofertas-ayudantia/{id}/cancelar` | Cancela oferta en estado permitido |
| POST | `/sesiones-ayudantia/{id}/iniciar` | Cambia sesión de `PROGRAMADA` a `EN_CURSO` |
| POST | `/sesiones-ayudantia/{id}/finalizar` | Cambia sesión de `EN_CURSO` a `FINALIZADA` |
| POST | `/sesiones-ayudantia/{id}/cancelar` | Cancela sesión en estado permitido |
| POST | `/inscripciones-ayudantia/{id}/cancelar` | Cancela inscripción activa o en espera |
| PATCH | `/inscripciones-ayudantia/{id}/asistencia` | Registra asistencia con `{ "asistencia": true }` |
| POST | `/postulaciones-auxiliar/{id}/aprobar` | Aprueba postulación, asigna auxiliar y rol |
| POST | `/postulaciones-auxiliar/{id}/rechazar` | Rechaza postulación pendiente |
| POST | `/postulaciones-auxiliar/{id}/cancelar` | Cancela postulación pendiente |

## 5. Query params de listado

| Parámetro | Ejemplo | Uso |
|---|---|---|
| `page` | `1` | Página actual |
| `per_page` | `15` | Tamaño de página |
| `search` | `contabilidad` | Búsqueda textual cuando el recurso la soporta |
| `sort_by` | `created_at` | Campo de orden |
| `sort_order` | `asc` / `desc` | Dirección de orden |

## 6. Errores comunes

| Error | Causa probable | Solución |
|---|---|---|
| `404 Not Found` | `base_url` tiene `/api` duplicado o ruta mal escrita | Usa `base_url=http://127.0.0.1:8000` |
| `401 Unauthorized` | Falta token Bearer | Ejecuta Register/Login primero |
| `422 Unprocessable Entity` | Body inválido, rol no permitido o dato duplicado | Revisa el JSON enviado y el mensaje de respuesta |
| `500 tokenable_id` | Migración vieja de Sanctum | Ejecuta `migrate:fresh --seed` con la versión corregida |

## 7. Cobertura de Postman y smoke test

La colección actual fue reconstruida para ser ejecutable de principio a fin.

```txt
Colección completa: docs/postman/collection.json
Smoke Postman:      docs/postman/smoke-test.collection.json
Rutas únicas:       82
Requests totales:   102
```

La diferencia entre rutas y requests es intencional: algunos endpoints necesitan datos distintos para no romper reglas de negocio. Por ejemplo:

- una oferta para publicar/cerrar;
- otra oferta para cancelar;
- una sesión para iniciar/finalizar;
- otra sesión para cancelar;
- varias postulaciones para aprobar, rechazar y cancelar;
- recursos separados para probar `DELETE` sin afectar dependencias previas.

Todos los `POST`, `PUT` y `PATCH` de recursos CRUD tienen payloads de prueba reales y no vacíos.
