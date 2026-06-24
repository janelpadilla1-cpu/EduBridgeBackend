# EduBridgeBackend

Backend API RESTful para el **Sistema de Ayudantías Universitarias EduBridge**.

El proyecto está pensado para correr localmente en **Laragon + MySQL** y mantiene una estructura compatible con el estilo generado por **IBEX CRUD Generator**: controladores delgados, validación con Form Requests, respuestas con API Resources, servicios de negocio y repositorios Eloquent.

## 1. Stack

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12 |
| Lenguaje | PHP 8.2+ |
| Base de datos local | MySQL / MariaDB en Laragon |
| ORM | Eloquent |
| Auth API | Laravel Sanctum, token Bearer opaco, no JWT |
| Validación | Form Requests |
| Respuestas | API Resources |
| Organización | Controllers, Services, Repositories, Models, Gateways |
| Registro de usuarios | Interno, sin consulta a directorio externo |
| Pruebas | PHPUnit Feature Tests + Smoke Test |
| Postman | Colección completa + colección smoke |

## 2. Instalación limpia en Laragon

Desde `C:\laragon\www\EduBridgeBackend`:

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Crear la base de datos desde la terminal de Laragon:

```bash
mysql -u root -e "DROP DATABASE IF EXISTS edubridge_backend; CREATE DATABASE edubridge_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Ejecutar migraciones y seeders:

```bash
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan serve
```

URL local esperada:

```txt
http://127.0.0.1:8000
```

## 3. Validación rápida

### Opción A: Smoke test PowerShell completo

Este script prueba auth, CRUD, acciones de negocio y DELETE con payloads reales.

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\smoke-test.ps1
```

### Opción B: Smoke test PHPUnit

```bash
composer test:smoke
```

### Opción C: Postman completo

La colección completa cubre 82 rutas únicas y usa 102 requests porque crea datos separados para aprobar, rechazar, cancelar y eliminar sin romper dependencias.

Importa estos archivos:

```txt
docs/postman/collection.json
docs/postman/smoke-test.collection.json
docs/postman/EduBridgeBackend.local.environment.json
```

En Postman, la variable `base_url` debe ser solamente:

```txt
http://127.0.0.1:8000
```

No pongas `/api` en `base_url`, porque la colección ya incluye `/api/v1` en cada endpoint.

## 4. Registro interno de usuarios

El registro ya no consulta ningún servidor externo de usuarios. El endpoint `POST /api/v1/auth/register` guarda todo internamente y asigna el rol enviado en el body.

Body mínimo recomendado:

```json
{
  "correo_institucional": "estudiante.demo@edu.bo",
  "nombre_completo": "Estudiante Demo",
  "rol": "ESTUDIANTE",
  "password": "Password123!",
  "password_confirmation": "Password123!"
}
```

Roles permitidos:

```txt
ESTUDIANTE, AUXILIAR, COORDINADOR, ADMINISTRADOR
```

`external_user_ref` es opcional. Si no se envía, el backend usa el correo institucional como referencia interna.

## 5. Endpoints principales

Base API:

```txt
{{base_url}}/api/v1
```

Endpoints públicos:

| Método | Ruta |
|---|---|
| GET | `/catalogos/estados` |
| POST | `/auth/register` |
| POST | `/auth/login` |

Endpoints protegidos con Bearer token:

| Módulo | Ruta base |
|---|---|
| Auth | `/auth/me`, `/auth/logout` |
| Usuarios | `/usuarios` |
| Cuentas | `/cuentas-usuario` |
| Roles | `/roles-usuario` |
| Usuarios/Roles | `/usuarios-roles` |
| Materias | `/materias` |
| Ofertas | `/ofertas-ayudantia` |
| Sesiones | `/sesiones-ayudantia` |
| Inscripciones | `/inscripciones-ayudantia` |
| Postulaciones | `/postulaciones-auxiliar` |
| Auxiliares | `/auxiliares-materia` |
| Disponibilidad | `/disponibilidad-auxiliar` |

Detalle completo en:

```txt
docs/endpoints/endpoints.md
```

## 6. Flujo recomendado en Postman

Ejecuta `docs/postman/collection.json` desde Collection Runner, de arriba hacia abajo.

La colección hace esto automáticamente:

1. Inicializa `run_id` y variables únicas.
2. Registra un usuario `ADMINISTRADOR` interno.
3. Guarda `access_token`.
4. Crea payloads reales para todos los módulos.
5. Prueba CRUD completo de todos los recursos.
6. Prueba acciones de negocio reales: publicar/cerrar/cancelar ofertas, iniciar/finalizar/cancelar sesiones, asistencia de inscripciones y aprobación/rechazo/cancelación de postulaciones.
7. Ejecuta los `DELETE` al final.

No ejecutes primero los requests de eliminación. Si cambias el orden, puedes provocar errores de claves foráneas o datos faltantes.

## 7. Estructura del proyecto

```txt
app/
  Gateways/                  Puertos e implementaciones fake permitidas, sin directorio externo de usuarios
  Http/Controllers/Api/V1/   Controladores REST
  Http/Requests/             Validaciones de entrada
  Http/Resources/            Serializadores JSON
  Models/                    Modelos Eloquent con UUID
  Repositories/              Persistencia y consultas
  Services/                  Casos de uso y reglas de negocio
database/
  migrations/                Estructura MySQL compatible con UUID
  seeders/                   Roles iniciales
docs/
  architecture/              Arquitectura y flujos
  endpoints/                 Contrato de rutas y OpenAPI
  postman/                   Colecciones y ambiente local
  testing/                   Guía de smoke test
scripts/                     Smoke tests ejecutables
tests/                       Feature tests
```

## 8. Decisiones técnicas importantes

- No se usa JWT. Sanctum genera tokens opacos guardados en `personal_access_tokens`.
- Los modelos usan UUID como llave primaria.
- `personal_access_tokens.tokenable_id` usa `uuidMorphs()` para evitar errores con Sanctum y UUID.
- `usuarios_roles` tiene `id` UUID porque también se expone como recurso CRUD.
- El registro de usuarios es interno: no se usa DirectorioUniversitarioGateway ni API externa para usuarios.
- El único gateway fake activo por defecto es Aulas, usado para sesiones.
- Redis no es requerido. Cache, sesión y colas quedan en modo local/file/sync.

## 9. IBEX CRUD Generator

El paquete queda como dependencia de desarrollo. Si necesitas generar un CRUD nuevo:

```bash
composer require ibex/crud-generator --dev
php artisan vendor:publish --tag=crud
php artisan make:crud nombre_modulo api
```

Después de generar, conserva la arquitectura del proyecto:

```txt
Controller -> FormRequest -> Service -> Repository -> Model -> Resource
```

## 10. Archivos clave

| Archivo | Uso |
|---|---|
| `.env.example` | Configuración local para Laragon/MySQL |
| `routes/api.php` | Rutas reales de la API |
| `docs/postman/collection.json` | Postman completo ejecutable: 82 rutas únicas / 102 requests |
| `docs/postman/smoke-test.collection.json` | Smoke test completo para Collection Runner |
| `docs/endpoints/endpoints.md` | Mapa legible de rutas |
| `docs/endpoints/openapi.yaml` | Contrato OpenAPI simplificado |
| `scripts/smoke-test.ps1` | Smoke test para Windows |
| `tests/Feature/SmokeApiTest.php` | Smoke test PHPUnit |
