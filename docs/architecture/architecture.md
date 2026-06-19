# Arquitectura técnica - EduBridgeBackend

## Objetivo

Construir una API RESTful para gestionar ayudantías universitarias: usuarios, cuentas locales, roles, materias, ofertas, sesiones, inscripciones, postulaciones y disponibilidad de auxiliares.

## Fuentes usadas

1. `docs/systemInfo/domainModel.puml`
2. `docs/systemInfo/caseUseModel.puml`
3. `docs/systemInfo/classDiagram.puml`
4. `docs/systemInfo/stateDiagram.puml`
5. `docs/systemInfo/activityDiagramMainFlow.puml`
6. `docs/systemInfo/componentDiagram.puml`
7. `docs/systemInfo/sequenceDiagram.puml`
8. `docs/systemInfo/deployDiagram.puml`
9. `docs/db/ddl.sql`
10. `prompt/index.md`, `prompt/programacionGeneral.md`, `prompt/programacionBackend.md`

## Stack

- Laravel API RESTful.
- Eloquent ORM.
- Form Requests.
- API Resources.
- Services.
- Repositories.
- Sanctum para autenticación por token opaco, no JWT.
- PostgreSQL recomendado para producción.
- SQLite permitido solo para desarrollo local o pruebas.

## Módulos

| Módulo | Responsabilidad |
|---|---|
| Auth | Crear cuenta local, iniciar sesión, cerrar sesión y consultar usuario autenticado. |
| Usuarios | Administrar usuarios universitarios sincronizados por referencia externa. |
| Roles | Gestionar roles `ESTUDIANTE`, `AUXILIAR`, `COORDINADOR`, `ADMINISTRADOR`. |
| Materias | Gestionar materias disponibles para ayudantías. |
| Ofertas | Crear, publicar, cerrar y cancelar ofertas de ayudantía. |
| Sesiones | Programar, iniciar, finalizar y cancelar sesiones. |
| Inscripciones | Inscribir estudiantes, lista de espera, cancelación y asistencia. |
| Auxiliares | Postulación, aprobación, rechazo, asignación y disponibilidad. |
| Gateways externos | Directorio universitario y sistema de aulas. |

## Decisiones de diseño

- Las aulas no son entidad interna. Se almacena `aula_ref_id` y `aula_nombre_cache`.
- El usuario institucional viene de un sistema externo. Se almacena `external_user_ref`.
- Los estados se guardan como texto y se validan en Form Requests/Services, no como ENUM nativo.
- Redis está excluido por defecto. Cache, cola y sesiones usan alternativas Laravel sin Redis.
- JWT está excluido. Sanctum se usa porque permite tokens de API opacos y se integra con Laravel.
- Los controllers son delgados. Las reglas de negocio viven en Services.
- Las consultas repetibles viven en Repositories.

## Supuestos documentados

- No se entregó contrato real del directorio universitario. Se dejó `FakeDirectorioUniversitarioGateway` para desarrollo.
- No se entregó contrato real del sistema externo de aulas. Se dejó `FakeAulaGateway` para desarrollo.
- No se entregó proveedor real de notificaciones. Se documenta como integración futura.
- No se implementaron workers porque el diagrama los marca como opcionales y el prompt vigente prohíbe Redis por defecto. Si se requieren colas, usar `QUEUE_CONNECTION=database`.
