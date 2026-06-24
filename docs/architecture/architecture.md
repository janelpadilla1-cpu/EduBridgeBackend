# Arquitectura técnica - EduBridgeBackend

## Objetivo

Construir una API RESTful para gestionar ayudantías universitarias: usuarios, cuentas locales, roles, materias, ofertas, sesiones, inscripciones, postulaciones y disponibilidad de auxiliares.

## Stack

- Laravel API RESTful.
- Eloquent ORM.
- Form Requests.
- API Resources.
- Services.
- Repositories.
- Sanctum para autenticación por token opaco, no JWT.
- MySQL/MariaDB recomendado para desarrollo local en Laragon.
- UUID como llave primaria en las entidades principales.

## Módulos

| Módulo | Responsabilidad |
|---|---|
| Auth | Registrar usuarios internamente, iniciar sesión, cerrar sesión y consultar usuario autenticado. |
| Usuarios | Administrar usuarios guardados en la base local. |
| Roles | Gestionar roles `ESTUDIANTE`, `AUXILIAR`, `COORDINADOR`, `ADMINISTRADOR`. |
| Materias | Gestionar materias disponibles para ayudantías. |
| Ofertas | Crear, publicar, cerrar y cancelar ofertas de ayudantía. |
| Sesiones | Programar, iniciar, finalizar y cancelar sesiones. |
| Inscripciones | Inscribir estudiantes, lista de espera, cancelación y asistencia. |
| Auxiliares | Postulación, aprobación, rechazo, asignación y disponibilidad. |
| Gateways | Aulas simuladas/locales para desarrollo. |

## Decisiones de diseño vigentes

- El registro de usuarios es **interno**. No se llama a un directorio universitario externo.
- `POST /api/v1/auth/register` recibe los datos base del usuario: correo, nombre completo, rol y contraseña.
- `external_user_ref` se conserva como identificador técnico interno/opcional; si no se envía, se usa el correo institucional.
- El rol inicial se asigna desde el body del registro mediante el campo `rol`.
- Las aulas no son entidad interna. Se almacena `aula_ref_id` y `aula_nombre_cache`; en desarrollo se usa `FakeAulaGateway`.
- Los estados se guardan como texto y se validan en Form Requests/Services.
- Redis está excluido por defecto. Cache, cola y sesiones usan alternativas Laravel sin Redis.
- JWT está excluido. Sanctum se usa porque permite tokens de API opacos y se integra con Laravel.
- Los controllers son delgados. Las reglas de negocio viven en Services.
- Las consultas repetibles viven en Repositories.

## Capas

```txt
HTTP Request
  -> FormRequest
  -> Controller
  -> Service
  -> Repository
  -> Model / Database
  -> Resource
  -> JSON Response
```

## Supuestos documentados

- No se usa proveedor externo para usuarios en esta versión.
- No se entregó contrato real del sistema externo de aulas; se mantiene `FakeAulaGateway` para desarrollo.
- No se entregó proveedor real de notificaciones. Se documenta como integración futura.
- No se implementaron workers porque el alcance local usa `QUEUE_CONNECTION=sync`.
