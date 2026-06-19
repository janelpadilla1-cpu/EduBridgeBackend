# Endpoints - EduBridgeBackend

Base URL local: `http://localhost:8000/api/v1`

## Autenticación

| Método | Ruta | Descripción | Protegido |
|---|---|---|---|
| POST | `/auth/register` | Crear cuenta local validando usuario externo | No |
| POST | `/auth/login` | Iniciar sesión | No |
| GET | `/auth/me` | Ver usuario autenticado | Sí |
| POST | `/auth/logout` | Cerrar sesión actual | Sí |

## Recursos CRUD

Todos los recursos CRUD protegidos usan token Bearer Sanctum.

| Recurso | Rutas | Descripción |
|---|---|---|
| Usuario | `/{usuarios}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |
| CuentaUsuario | `/{cuentas-usuario}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |
| RolUsuario | `/{roles-usuario}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |
| UsuarioRol | `/{usuarios-roles}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |
| Materia | `/{materias}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |
| OfertaAyudantia | `/{ofertas-ayudantia}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |
| SesionAyudantia | `/{sesiones-ayudantia}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |
| InscripcionAyudantia | `/{inscripciones-ayudantia}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |
| PostulacionAuxiliar | `/{postulaciones-auxiliar}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |
| AuxiliarMateria | `/{auxiliares-materia}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |
| DisponibilidadAuxiliar | `/{disponibilidad-auxiliar}` | CRUD RESTful con `GET`, `POST`, `GET /{id}`, `PUT/PATCH /{id}`, `DELETE /{id}` |

## Acciones de negocio

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/ofertas-ayudantia/{id}/publicar` | Publica una oferta en borrador |
| POST | `/ofertas-ayudantia/{id}/cerrar` | Cierra una oferta publicada |
| POST | `/ofertas-ayudantia/{id}/cancelar` | Cancela una oferta |
| POST | `/sesiones-ayudantia/{id}/iniciar` | Cambia sesión a EN_CURSO |
| POST | `/sesiones-ayudantia/{id}/finalizar` | Cambia sesión a FINALIZADA |
| POST | `/sesiones-ayudantia/{id}/cancelar` | Cancela sesión programada |
| POST | `/inscripciones-ayudantia/{id}/cancelar` | Cancela inscripción |
| PATCH | `/inscripciones-ayudantia/{id}/asistencia` | Registra asistencia o inasistencia |
| POST | `/postulaciones-auxiliar/{id}/aprobar` | Aprueba postulación y asigna auxiliar |
| POST | `/postulaciones-auxiliar/{id}/rechazar` | Rechaza postulación pendiente |
| POST | `/postulaciones-auxiliar/{id}/cancelar` | Cancela postulación pendiente |
| GET | `/catalogos/estados` | Devuelve estados permitidos |

## Parámetros comunes de listado

- `search`: texto de búsqueda.
- `page`: número de página.
- `per_page`: registros por página, máximo 100.
- `sort_by`: campo permitido por recurso.
- `sort_order`: `asc` o `desc`.
