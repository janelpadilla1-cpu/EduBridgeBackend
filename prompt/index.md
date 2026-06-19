# Prompt raíz del proyecto EduBridgeBackend

## 0. Objetivo del proyecto

Generar una API RESTful completa para **EduBridgeBackend**, un sistema de ayudantías universitarias donde estudiantes pueden inscribirse a sesiones de apoyo académico y postularse como auxiliares, mientras coordinadores académicos administran materias, ofertas, sesiones, postulaciones y asistencia.

El entregable debe ser un backend Laravel de producción, mantenible, documentado y alineado con los diagramas y el DDL incluidos en este repositorio.

---

## 1. Stack obligatorio

El proyecto debe desarrollarse con:

- PHP moderno.
- Laravel como framework backend principal.
- Eloquent ORM como capa de persistencia.
- Migraciones Laravel para estructura de base de datos.
- Form Requests para validación de entrada.
- API Resources para respuestas JSON.
- Controllers API delgados.
- Services para reglas de negocio.
- Repositories para acceso a datos cuando el módulo tenga lógica suficiente.
- Policies o Gates cuando se agreguen permisos finos.
- Laravel Sanctum si se requiere autenticación por API.
- PostgreSQL como base recomendada para producción.
- SQLite solo para desarrollo local o pruebas.
- Estructura compatible con IBEX CRUD Generator.

No usar por defecto:

- Redis.
- JWT.
- Express.
- NestJS.
- Node.js.
- TypeScript.
- Sequelize.
- Zod.

---

## 2. Lectura obligatoria de prompts base

Antes de generar o modificar código, leer y aplicar:

```txt
prompt/programacionGeneral.md
prompt/programacionBackend.md
```

`programacionBackend.md` tiene prioridad para decisiones específicas de backend Laravel e IBEX CRUD Generator.

---

## 3. Fuentes funcionales del sistema

La fuente principal del dominio está en:

```txt
docs/systemInfo/domainModel.puml
docs/systemInfo/caseUseModel.puml
docs/systemInfo/classDiagram.puml
docs/systemInfo/stateDiagram.puml
docs/systemInfo/activityDiagramMainFlow.puml
docs/systemInfo/componentDiagram.puml
docs/systemInfo/sequenceDiagram.puml
docs/systemInfo/deployDiagram.puml
docs/db/ddl.sql
```

Prioridad de lectura:

1. `domainModel.puml`
2. `ddl.sql`
3. `caseUseModel.puml`
4. `classDiagram.puml`
5. `stateDiagram.puml`
6. `activityDiagramMainFlow.puml`
7. `componentDiagram.puml`
8. `sequenceDiagram.puml`
9. `deployDiagram.puml`

---

## 4. Entidades obligatorias

Generar modelos, migraciones, requests, resources, services, repositories y controllers API para:

- `Usuario`
- `CuentaUsuario`
- `RolUsuario`
- `UsuarioRol`
- `Materia`
- `OfertaAyudantia`
- `SesionAyudantia`
- `InscripcionAyudantia`
- `PostulacionAuxiliar`
- `AuxiliarMateria`
- `DisponibilidadAuxiliar`

Los nombres de tablas deben respetar el DDL:

- `usuarios`
- `cuentas_usuario`
- `roles_usuario`
- `usuarios_roles`
- `materias`
- `ofertas_ayudantia`
- `sesiones_ayudantia`
- `inscripciones_ayudantia`
- `postulaciones_auxiliar`
- `auxiliares_materia`
- `disponibilidad_auxiliar`

---

## 5. Casos de uso obligatorios

Implementar como mínimo:

### Auth

- Crear cuenta local registrando contraseña.
- Validar usuario universitario mediante gateway externo.
- Iniciar sesión.
- Ver usuario autenticado.
- Cerrar sesión.

### Estudiante

- Consultar materias con ayudantías.
- Ver horarios disponibles.
- Inscribirse a sesión.
- Cancelar inscripción.
- Ver inscripciones.

### Auxiliar

- Postularse como auxiliar.
- Registrar disponibilidad.
- Ver sesiones asignadas.

### Coordinador académico

- Crear materia.
- Crear oferta de ayudantía.
- Publicar, cerrar o cancelar oferta.
- Programar sesión.
- Asignar auxiliar.
- Aprobar o rechazar postulación.
- Registrar asistencia.

---

## 6. Reglas de negocio principales

- El usuario solo registra contraseña; sus datos base provienen del sistema universitario externo.
- `external_user_ref` no es FK interna; es referencia externa.
- El aula no se administra internamente; se guarda `aula_ref_id` y opcionalmente `aula_nombre_cache`.
- Una cuenta local pertenece a un solo usuario.
- Un usuario puede tener varios roles.
- Una materia puede tener varias ofertas.
- Una oferta puede tener varias sesiones.
- Una sesión puede tener un auxiliar asignado o ninguno.
- Una inscripción no puede duplicarse para el mismo usuario y sesión.
- Si la sesión no tiene cupo, la inscripción entra como `EN_ESPERA`.
- La aprobación de una postulación crea o reactiva la relación `AuxiliarMateria` y asigna rol `AUXILIAR`.
- Los estados se guardan como texto, no como ENUM nativo.

---

## 7. Estados válidos

### OfertaAyudantia

- `BORRADOR`
- `PUBLICADA`
- `CERRADA`
- `CANCELADA`

### SesionAyudantia

- `PROGRAMADA`
- `EN_CURSO`
- `FINALIZADA`
- `CANCELADA`

### InscripcionAyudantia

- `INSCRITO`
- `EN_ESPERA`
- `CANCELADO`
- `ASISTIO`
- `NO_ASISTIO`

### PostulacionAuxiliar

- `PENDIENTE`
- `APROBADA`
- `RECHAZADA`
- `CANCELADA`

### AuxiliarMateria

- `ACTIVO`
- `INACTIVO`

---

## 8. Estructura esperada

```txt
app/
  Gateways/
  Http/
    Controllers/Api/V1/
    Requests/
    Resources/
  Models/
  Providers/
  Repositories/
    Contracts/
    Eloquent/
  Services/
bootstrap/
config/
database/
  factories/
  migrations/
  seeders/
docs/
  architecture/
  endpoints/
  postman/
  systemInfo/
prompt/
routes/
tests/
```

---

## 9. Endpoints

Usar versionado `/api/v1`.

Para CRUD estándar usar rutas RESTful con `Route::apiResource`:

```txt
GET    /api/v1/materias
POST   /api/v1/materias
GET    /api/v1/materias/{id}
PUT    /api/v1/materias/{id}
PATCH  /api/v1/materias/{id}
DELETE /api/v1/materias/{id}
```

Agregar rutas de negocio solo cuando no sean CRUD puro:

```txt
POST /api/v1/ofertas-ayudantia/{id}/publicar
POST /api/v1/ofertas-ayudantia/{id}/cerrar
POST /api/v1/ofertas-ayudantia/{id}/cancelar
POST /api/v1/sesiones-ayudantia/{id}/iniciar
POST /api/v1/sesiones-ayudantia/{id}/finalizar
POST /api/v1/sesiones-ayudantia/{id}/cancelar
POST /api/v1/inscripciones-ayudantia/{id}/cancelar
PATCH /api/v1/inscripciones-ayudantia/{id}/asistencia
POST /api/v1/postulaciones-auxiliar/{id}/aprobar
POST /api/v1/postulaciones-auxiliar/{id}/rechazar
POST /api/v1/postulaciones-auxiliar/{id}/cancelar
```

---

## 10. Documentación obligatoria

Generar y mantener:

```txt
docs/endpoints/endpoints.md
docs/endpoints/openapi.yaml
docs/endpoints/README.md
docs/architecture/architecture.md
docs/architecture/flows.md
docs/architecture/README.md
docs/postman/collection.json
docs/postman/README.md
README.md
```

---

## 11. Validación final

Antes de entregar verificar:

- No existe Redis por defecto.
- No existe JWT.
- No existe Sequelize.
- No existe Zod.
- No existe Express/NestJS.
- Los controllers son delgados.
- Las reglas de negocio están en Services.
- Las consultas persistentes están en Repositories.
- Las entradas se validan con Form Requests.
- Las salidas usan API Resources.
- Las migraciones respetan el DDL.
- Las rutas están versionadas en `/api/v1`.
- La documentación y Postman están actualizados.
- Los supuestos de integraciones externas están documentados.
