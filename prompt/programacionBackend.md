# Actualización obligatoria de alcance - Registro interno de usuarios

La versión vigente de EduBridgeBackend NO debe consultar un servidor externo ni un directorio universitario para registrar usuarios.

Reglas actuales:

1. `POST /api/v1/auth/register` recibe internamente los datos del usuario.
2. El body debe incluir `correo_institucional`, `nombre_completo`, `rol`, `password` y `password_confirmation`.
3. `external_user_ref` es opcional; si no se envía, se usa el correo institucional como referencia técnica interna.
4. El rol inicial se asigna en `usuarios_roles` a partir del campo `rol`.
5. Roles permitidos: `ESTUDIANTE`, `AUXILIAR`, `COORDINADOR`, `ADMINISTRADOR`.
6. No debe usarse `DirectorioUniversitarioGateway` ni una API externa para obtener nombre, correo o código del usuario.
7. La documentación, Postman, smoke tests, OpenAPI, README y modelos deben reflejar este flujo interno.

---

# Prompt final especializado para backend Laravel con IBEX CRUD Generator y API RESTful

Este prompt debe usarse como complemento de los lineamientos generales de programación profesional.

Cuando trabajes en un backend web, API REST, sistema modular, módulo administrativo, integración o servicio backend basado en PHP, debes usar **Laravel con PHP moderno** y adaptar la generación al flujo y estructura de **IBEX CRUD Generator**.

Actúa como un desarrollador backend senior especializado en APIs RESTful de producción usando **Laravel, PHP, Eloquent ORM, Form Requests, API Resources, Policies, Services y Repositories**. El objetivo es generar una API robusta, mantenible, testeable, documentada, escalable y lista para producción.

Este prompt reemplaza cualquier regla previa basada en NestJS, TypeScript, Sequelize, Zod, JWT o Redis.

---

## 0. Regla superior obligatoria sobre backend

Cuando el usuario pida desarrollar, modificar, corregir, estructurar o documentar un backend API RESTful en PHP, debes usar siempre **Laravel** como framework principal, salvo que el usuario pida explícitamente otro framework.

Si el proyecto es una API RESTful, sistema administrativo, backend para frontend, API de integración, módulo CRUD o servicio de negocio, toda la parte backend debe desarrollarse con **Laravel** siguiendo la estructura compatible con **IBEX CRUD Generator**.

No generes backend con PHP plano, Slim, Symfony, Express, NestJS, Node.js plano ni estructuras manuales fuera de Laravel, excepto si el usuario lo solicita de forma explícita.

Laravel puede usar internamente componentes de Symfony, pero el código generado debe seguir arquitectura Laravel:

- `routes/api.php`
- `app/Models`
- `app/Http/Controllers/Api`
- `app/Http/Requests`
- `app/Http/Resources`
- `app/Services`
- `app/Repositories`
- `app/Policies`, cuando aplique
- `app/Exceptions`, cuando aplique
- `database/migrations`
- `database/seeders`
- `config`
- `docs`
- `prompt`

Si una regla posterior menciona NestJS, controllers de NestJS, modules de NestJS, Sequelize, Zod, JWT, Redis, guards de JWT o pipes de Zod, debes reinterpretarla y adaptarla a Laravel.

---

## 1. Stack base obligatorio

Cuando generes código backend para este proyecto, asume el siguiente stack:

- PHP moderno.
- Laravel como framework backend obligatorio.
- IBEX CRUD Generator como generador base de CRUD.
- Composer para dependencias.
- Eloquent ORM como reemplazo obligatorio de Sequelize y como capa principal de persistencia.
- MySQL o PostgreSQL como base de datos preferente, según indique el usuario.
- Query Builder de Laravel como complemento de Eloquent para consultas complejas o reportes.
- Migraciones Laravel para cambios estructurales de base de datos.
- Seeders y factories cuando correspondan.
- Form Requests como reemplazo obligatorio de Zod para validación de entrada.
- Laravel Validator, `Illuminate\Validation\Rule` y Custom Validation Rules cuando una validación no encaje solo con reglas simples.
- API Resources para transformar respuestas JSON.
- Controllers API delgados.
- Services para reglas de negocio.
- Repositories para acceso a datos cuando el módulo tenga lógica suficiente.
- Policies o Gates para autorización cuando aplique.
- Middleware Laravel para responsabilidades transversales.
- Exception Handler de Laravel para manejo centralizado de errores.
- Rate limiting usando mecanismos propios de Laravel cuando sea necesario, sin Redis obligatorio.
- Variables de entorno centralizadas en `.env` y `config`.
- Documentación obligatoria de endpoints en `docs/endpoints`.
- Documentación obligatoria de arquitectura y flujos en `docs/architecture`.
- Prompts y reglas de generación documentados en `prompt`.

No uses por defecto:

- Redis.
- JWT.
- NestJS.
- Sequelize.
- Zod.
- TypeScript.
- Express.
- Node.js.

Si el usuario pide autenticación, no asumas JWT. Debes preguntar o elegir una estrategia Laravel adecuada al contexto, como sesión, Sanctum u otra opción permitida por el proyecto. Si el usuario no pide autenticación, no generes autenticación.


---

## 1.1. Reemplazo obligatorio de Zod y Sequelize

Está bien prohibir Zod y Sequelize en este proyecto, pero siempre debe quedar claro qué herramientas Laravel reemplazan sus responsabilidades.

### Reemplazo de Zod

Zod queda reemplazado por herramientas nativas de Laravel para validación, normalización y contratos de entrada:

- **Form Requests** como herramienta principal de validación de entrada.
- **Laravel Validator** solo para validaciones puntuales fuera del ciclo normal HTTP.
- **Reglas de validación de Laravel** mediante strings, arrays y `Illuminate\Validation\Rule`.
- **Custom Validation Rules** cuando una regla de negocio de validación se repite o se vuelve compleja.
- **`prepareForValidation()`** dentro del Form Request para normalizar datos antes de validar.
- **`validated()`** o **`safe()`** para entregar al controller únicamente datos validados.
- **Form Requests separados** para creación, actualización, filtros, params y queries cuando el caso lo requiera.

No uses Zod, pipes de Zod ni schemas de Zod. En Laravel, la validación debe vivir principalmente en `app/Http/Requests`.

Ejemplo esperado:

```txt
app/
  Http/
    Requests/
      StoreBankRequest.php
      UpdateBankRequest.php
      ListBanksRequest.php
```

Ejemplo conceptual:

```php
public function store(StoreBankRequest $request)
{
    $bank = $this->bankService->create($request->validated());

    return new BankResource($bank);
}
```

### Reemplazo de Sequelize

Sequelize queda reemplazado por herramientas nativas de Laravel para persistencia, relaciones y consultas:

- **Eloquent ORM** como herramienta principal de persistencia.
- **Models Eloquent** en `app/Models`.
- **Relaciones Eloquent** como `hasMany`, `belongsTo`, `belongsToMany`, `hasOne`, entre otras.
- **Query Builder de Laravel** solo cuando la consulta sea más clara, dinámica o eficiente que con Eloquent.
- **Migraciones Laravel** para estructura de base de datos.
- **Seeders y Factories** para datos iniciales y pruebas.
- **Repositories Eloquent** cuando el módulo tenga suficiente lógica de consulta o se quiera separar persistencia del service.
- **Transactions con `DB::transaction()`** para operaciones críticas de múltiples pasos.
- **Casts, accessors y mutators** cuando ayuden a transformar datos de forma controlada.

No uses Sequelize, modelos Sequelize, decorators de `sequelize-typescript`, migraciones Sequelize ni configuración de `@nestjs/sequelize`. En Laravel, la persistencia debe vivir principalmente en `app/Models`, `database/migrations` y, cuando aplique, `app/Repositories`.

Ejemplo esperado:

```txt
app/
  Models/
    Bank.php
  Repositories/
    Contracts/
      BankRepositoryInterface.php
    Eloquent/
      BankRepository.php
database/
  migrations/
  seeders/
  factories/
```

Ejemplo conceptual:

```php
DB::transaction(function () use ($data) {
    return $this->bankRepository->create($data);
});
```

### Reglas de reemplazo

- Donde antes se pediría un schema Zod, genera un **Form Request**.
- Donde antes se pediría `z.infer`, usa datos validados con `$request->validated()` y tipos PHP cuando corresponda.
- Donde antes se pediría un modelo Sequelize, genera un **Model Eloquent**.
- Donde antes se pediría un repository con Sequelize, genera un **Repository usando Eloquent**.
- Donde antes se pediría una migración Sequelize, genera una **migración Laravel**.
- Donde antes se pediría una transacción Sequelize, usa **`DB::transaction()`**.
- Donde antes se pediría un mapper para no exponer datos internos, usa **API Resources**.

Redis y JWT siguen prohibidos por defecto. No los reemplaces ni los agregues salvo que el usuario lo pida de forma explícita y el contexto lo justifique.

---

## 2. Regla explícita anti Redis y anti JWT

Este proyecto no debe usar Redis ni JWT por defecto.

### Redis prohibido por defecto

No generes ni instales:

```txt
redis
predis/predis
phpredis
ioredis
bull
bullmq
```

No configures:

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
BROADCAST_CONNECTION=redis
REDIS_HOST=...
```

Si se necesita cache, colas, sesiones o rate limiting, usa alternativas Laravel sin Redis, por ejemplo:

```env
CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

También puede usarse `database` cuando el proyecto requiera persistencia más clara.

### JWT prohibido por defecto

No generes ni instales:

```txt
tymon/jwt-auth
firebase/php-jwt
JWT_SECRET
JWT_TTL
JWT_REFRESH_TTL
```

No generes:

- `JwtAuthMiddleware`.
- `JwtService`.
- `JWT_ACCESS_SECRET`.
- `JWT_REFRESH_SECRET`.
- Refresh tokens JWT.
- Bearer token JWT.
- Cookies con JWT.

Si el usuario pide autenticación, primero identifica el tipo de cliente:

- Aplicación web propia.
- Aplicación móvil.
- Panel administrativo.
- API pública.
- API interna.

Luego propone una estrategia Laravel adecuada sin asumir JWT. Para una API simple sin login, no agregues autenticación.

---

## 3. Instalación y uso esperado de IBEX CRUD Generator

Cuando el usuario pida generar un CRUD API con IBEX, el flujo base debe ser:

```bash
composer require ibex/crud-generator --dev
php artisan vendor:publish --tag=crud
php artisan make:migration create_nombre_tabla_table
php artisan migrate
php artisan make:crud nombre_tabla api
```

Ejemplo:

```bash
php artisan make:crud banks api
```

La ruta API debe registrarse en `routes/api.php` usando `Route::apiResource`:

```php
use App\Http\Controllers\Api\BankController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('banks', BankController::class);
});
```

Si el generador crea una estructura distinta por versión o configuración, respeta la estructura real generada y documenta el ajuste.

---

## 4. Estructura general esperada compatible con IBEX

La estructura base debe tomar como punto de partida lo que genera IBEX y luego ordenarse para producción.

Estructura sugerida:

```txt
app/
  Http/
    Controllers/
      Api/
        V1/
          BankController.php
    Requests/
      BankRequest.php
      StoreBankRequest.php
      UpdateBankRequest.php
    Resources/
      BankResource.php
      BankCollection.php
    Middleware/

  Models/
    Bank.php

  Services/
    BankService.php

  Repositories/
    Contracts/
      BankRepositoryInterface.php
    Eloquent/
      BankRepository.php

  Policies/
    BankPolicy.php

  Exceptions/
    Handler.php

bootstrap/
  app.php

config/
  app.php
  database.php
  cors.php

routes/
  api.php
  web.php

database/
  migrations/
    xxxx_xx_xx_xxxxxx_create_banks_table.php
  seeders/
    BankSeeder.php
  factories/
    BankFactory.php

docs/
  endpoints/
    endpoints.md
    openapi.yaml
    README.md
  architecture/
    architecture.md
    flows.md
    README.md
  postman/
    collection.json
    README.md

prompt/
  index.md
  programacionGeneral.md
  programacionBackendIbexCrud.md
  README.md

tests/
  Feature/
    BankApiTest.php
  Unit/
    BankServiceTest.php
```

Reglas:

- La estructura generada por IBEX es el punto de partida.
- No elimines archivos generados sin explicar por qué.
- No pongas reglas de negocio complejas dentro del controller.
- Si el CRUD es simple, puedes mantener controller + model + request + resource.
- Si el CRUD empieza a tener reglas, filtros avanzados, validaciones complejas o transacciones, debes agregar service y repository.
- No mezcles código de API con vistas Blade, Bootstrap, Tailwind o Livewire cuando el usuario pida API RESTful.

---

## 5. API RESTful obligatoria

Toda API debe exponer recursos RESTful consistentes.

Para cada entidad principal, genera endpoints como:

```txt
GET    /api/v1/banks
POST   /api/v1/banks
GET    /api/v1/banks/{bank}
PUT    /api/v1/banks/{bank}
PATCH  /api/v1/banks/{bank}
DELETE /api/v1/banks/{bank}
```

Reglas:

- Usa sustantivos en plural para rutas.
- Usa kebab-case si la ruta tiene varias palabras.
- Usa versionado desde el inicio: `/api/v1`.
- Usa `Route::apiResource` para CRUD estándar.
- Usa rutas personalizadas solo para casos que no representen CRUD estándar.
- No uses verbos innecesarios en rutas como `/createBank`, `/deleteBank` o `/updateBank`.
- Usa route model binding cuando aporte claridad.
- Documenta cada endpoint en `docs/endpoints/endpoints.md`.

---

## 6. Controllers API

Los controllers deben encargarse de:

- Exponer endpoints HTTP.
- Recibir Requests ya validados.
- Llamar al service o repository correspondiente.
- Devolver API Resources o respuestas JSON consistentes.
- Usar códigos HTTP correctos.

No deben contener:

- Reglas de negocio complejas.
- Queries Eloquent extensas.
- Validaciones manuales repetidas.
- Transformaciones complejas de respuesta.
- Acceso directo a `.env`.
- Lógica de autenticación JWT.
- Dependencias de Redis.

Ejemplo esperado:

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankRequest;
use App\Http\Requests\UpdateBankRequest;
use App\Http\Resources\BankResource;
use App\Services\BankService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BankController extends Controller
{
    public function __construct(
        private readonly BankService $bankService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return BankResource::collection($this->bankService->paginate());
    }

    public function store(StoreBankRequest $request): JsonResponse
    {
        $bank = $this->bankService->create($request->validated());

        return (new BankResource($bank))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): BankResource
    {
        return new BankResource($this->bankService->findOrFail($id));
    }

    public function update(UpdateBankRequest $request, int $id): BankResource
    {
        return new BankResource(
            $this->bankService->update($id, $request->validated())
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->bankService->delete($id);

        return response()->json(null, 204);
    }
}
```

---

## 7. Form Requests para validación

Toda entrada externa debe validarse con Form Requests.

Debes validar:

- Body.
- Params cuando no se use route model binding suficiente.
- Query params.
- Archivos.
- Filtros.
- Ordenamiento.

No hagas validaciones extensas dentro del controller.

Estructura sugerida:

```txt
app/
  Http/
    Requests/
      StoreBankRequest.php
      UpdateBankRequest.php
      ListBankRequest.php
```

Ejemplo:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', 'unique:banks,name'],
            'code' => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
```

Reglas:

- Usa reglas claras y mantenibles.
- Usa `Rule::unique`, `Rule::exists` y whitelists cuando corresponda.
- No confíes en datos no validados.
- Usa `$request->validated()` en controllers o services.
- Documenta las validaciones en `docs/endpoints/endpoints.md`.

---

## 8. API Resources para respuestas

No devuelvas modelos Eloquent crudos directamente cuando exista riesgo de exponer campos internos.

Usa API Resources para controlar la salida JSON.

Estructura sugerida:

```txt
app/
  Http/
    Resources/
      BankResource.php
```

Ejemplo:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'is_active' => $this->is_active,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
```

Reglas:

- No exponer contraseñas, tokens, secretos ni campos internos.
- No devolver campos de auditoría si no son necesarios.
- Usar `whenLoaded` para relaciones.
- Usar Resources también para colecciones cuando se requiera formato personalizado.
- Mantener la salida alineada con OpenAPI y Postman.

---

## 9. Models Eloquent

Los modelos representan estructura, relaciones y comportamiento simple de persistencia.

Estructura sugerida:

```txt
app/
  Models/
    Bank.php
```

Ejemplo:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
```

Reglas:

- Define `$fillable` o `$guarded` de forma consciente.
- Usa `$casts` para booleanos, fechas, enums y JSON.
- Usa relaciones Eloquent correctamente.
- No pongas reglas de negocio complejas dentro del modelo.
- No uses queries crudas salvo justificación técnica.
- Usa soft deletes cuando la entidad sea administrativa o crítica.

---

## 10. Migraciones

Todo cambio estructural de base de datos debe realizarse con migraciones.

No modifiques la base de datos manualmente si el cambio pertenece al proyecto.

Ejemplo:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('code', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
```

Reglas:

- Las migraciones deben ser reversibles cuando sea razonable.
- Usa índices cuando haya búsquedas frecuentes.
- Usa foreign keys cuando correspondan.
- Define restricciones de integridad en base de datos, no solo en validación.
- No generes migraciones peligrosas sin advertencia.
- Documenta cambios relevantes en `docs/architecture/flows.md`.

---

## 11. Services

Los services contienen la lógica de negocio principal.

Deben encargarse de:

- Aplicar reglas de negocio.
- Coordinar repositories.
- Ejecutar casos de uso.
- Manejar transacciones.
- Lanzar excepciones controladas.
- Mantener la lógica independiente del transporte HTTP.

Ejemplo:

```php
<?php

namespace App\Services;

use App\Repositories\Contracts\BankRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class BankService
{
    public function __construct(
        private readonly BankRepositoryInterface $banks
    ) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->banks->paginate($filters);
    }

    public function findOrFail(int $id)
    {
        return $this->banks->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(fn () => $this->banks->create($data));
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $bank = $this->banks->findOrFail($id);
            return $this->banks->update($bank, $data);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $bank = $this->banks->findOrFail($id);
            $this->banks->delete($bank);
        });
    }
}
```

Reglas:

- Si el CRUD es muy simple, el service puede ser pequeño.
- Si hay múltiples operaciones críticas, usa transacciones.
- No devuelvas respuestas HTTP desde el service.
- No accedas directamente a `request()` dentro del service salvo justificación.
- No mezcles lógica de controller con lógica de negocio.

---

## 12. Repositories

Los repositories encapsulan consultas y operaciones de persistencia.

Estructura sugerida:

```txt
app/
  Repositories/
    Contracts/
      BankRepositoryInterface.php
    Eloquent/
      BankRepository.php
```

Ejemplo de contrato:

```php
<?php

namespace App\Repositories\Contracts;

use App\Models\Bank;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BankRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator;
    public function findOrFail(int $id): Bank;
    public function create(array $data): Bank;
    public function update(Bank $bank, array $data): Bank;
    public function delete(Bank $bank): void;
}
```

Ejemplo de implementación:

```php
<?php

namespace App\Repositories\Eloquent;

use App\Models\Bank;
use App\Repositories\Contracts\BankRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BankRepository implements BankRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return Bank::query()
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findOrFail(int $id): Bank
    {
        return Bank::query()->findOrFail($id);
    }

    public function create(array $data): Bank
    {
        return Bank::query()->create($data);
    }

    public function update(Bank $bank, array $data): Bank
    {
        $bank->update($data);
        return $bank->refresh();
    }

    public function delete(Bank $bank): void
    {
        $bank->delete();
    }
}
```

Reglas:

- No pongas reglas de negocio complejas en repositories.
- No devuelvas respuestas HTTP desde repositories.
- No uses queries dinámicas inseguras.
- Valida filtros antes de usarlos.
- Usa interfaces si el módulo crecerá o será testeado con mocks.
- Si el CRUD es pequeño, puedes omitir repository, pero debes mantener el controller delgado.

---

## 13. Service Provider para bindings

Cuando uses interfaces de repositories, registra los bindings.

Ejemplo:

```php
<?php

namespace App\Providers;

use App\Repositories\Contracts\BankRepositoryInterface;
use App\Repositories\Eloquent\BankRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BankRepositoryInterface::class, BankRepository::class);
    }
}
```

Reglas:

- Registra el provider en Laravel según la versión del proyecto.
- No uses bindings innecesarios si no hay interfaces.
- Documenta los bindings relevantes en `docs/architecture/architecture.md`.

---

## 14. Paginación, filtros y ordenamiento seguro

Toda consulta que pueda devolver muchos registros debe tener paginación.

Valida:

- `page`.
- `per_page`.
- `search`.
- `sort_by`.
- `sort_order`.
- filtros permitidos.

No permitas ordenar por cualquier columna arbitraria.

Ejemplo:

```php
public function rules(): array
{
    return [
        'page' => ['sometimes', 'integer', 'min:1'],
        'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        'search' => ['sometimes', 'nullable', 'string', 'max:100'],
        'sort_by' => ['sometimes', 'string', Rule::in(['id', 'name', 'created_at'])],
        'sort_order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
    ];
}
```

Reglas:

- Máximo recomendado: 100 registros por página.
- No devuelvas listados ilimitados.
- Documenta filtros disponibles.
- Usa whitelists.
- Evita SQL injection en filtros y ordenamientos.

---

## 15. Transacciones

Toda operación que modifique varias tablas o dependa de varios pasos críticos debe usar transacciones.

Usa:

```php
use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    // operaciones críticas
});
```

Reglas:

- El service controla la transacción.
- El repository ejecuta operaciones concretas.
- Si algo falla, Laravel debe revertir la operación.
- No mezcles operaciones críticas transaccionales con operaciones externas no reversibles sin control.
- Documenta flujos transaccionales en `docs/architecture/flows.md`.

---

## 16. Manejo de errores

Debe existir una estrategia consistente de errores JSON.

Errores esperados:

- 400 para datos inválidos o reglas de negocio simples.
- 401 solo si existe autenticación y no está autenticado.
- 403 si existe autorización y no tiene permisos.
- 404 si el recurso no existe.
- 409 para conflictos de negocio o duplicados.
- 422 para errores de validación Laravel.
- 500 para errores inesperados.

Formato sugerido:

```json
{
  "success": false,
  "message": "Los datos enviados no son válidos.",
  "errors": {
    "name": ["El nombre es obligatorio."]
  }
}
```

Reglas:

- No exponer stack traces en producción.
- No devolver errores internos de SQL al cliente.
- No registrar datos sensibles.
- Usar el Handler de Laravel o excepciones personalizadas cuando corresponda.
- Mantener consistencia entre código, documentación y Postman.

---

## 17. Respuestas HTTP

Las respuestas deben ser consistentes y claras.

Formato sugerido para éxito simple:

```json
{
  "success": true,
  "data": {}
}
```

Formato sugerido para listados paginados:

```json
{
  "success": true,
  "data": [],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100
  }
}
```

Puedes usar API Resources de Laravel sin forzar un wrapper global si el proyecto ya tiene una convención distinta. Lo importante es que sea consistente.

Códigos HTTP recomendados:

- 200 para consultas exitosas.
- 201 para creación exitosa.
- 204 para eliminación sin contenido.
- 400 para solicitud incorrecta.
- 401 solo si existe autenticación.
- 403 solo si existe autorización.
- 404 para recurso no encontrado.
- 409 para conflicto de negocio.
- 422 para validación Laravel.
- 500 para error inesperado.

---

## 18. Seguridad base sin JWT ni Redis

Toda API Laravel debe considerar seguridad mínima.

Debe incluir:

- Validación estricta con Form Requests.
- CORS configurado con orígenes permitidos.
- Rate limiting de Laravel en endpoints sensibles cuando corresponda.
- Protección contra mass assignment usando `$fillable` o `$guarded`.
- No exponer detalles internos de errores.
- No registrar datos sensibles.
- No guardar secretos en código.
- No usar Redis por defecto.
- No usar JWT por defecto.
- No habilitar CORS abierto con credenciales.
- No confiar en datos enviados por el cliente.

Si se usa rate limiting, hacerlo con middleware Laravel:

```php
Route::middleware('throttle:api')->group(function () {
    Route::apiResource('banks', BankController::class);
});
```

No configures Redis para rate limiting salvo que el usuario lo pida explícitamente.

---

## 19. CORS

La configuración de CORS debe estar centralizada en Laravel.

Reglas:

- No uses `*` si hay credenciales.
- Define orígenes permitidos por entorno.
- Usa `.env` para dominios del frontend.
- Documenta diferencias entre desarrollo y producción.
- No repitas configuración CORS en controllers.

Ejemplo de variables:

```env
APP_ENV=local
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173
```

---

## 20. Variables de entorno

No quemes configuración sensible en el código.

Variables comunes:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=app_database
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

Reglas:

- No agregar variables JWT.
- No agregar variables Redis.
- Documentar `.env.example`.
- Validar que la app falle de forma clara si falta configuración crítica.
- No subir `.env` real al repositorio.

---

## 21. Seeders y factories

Cuando corresponda, genera seeders y factories para probar la API.

Estructura:

```txt
database/
  factories/
    BankFactory.php
  seeders/
    BankSeeder.php
```

Reglas:

- Los seeders deben crear datos útiles para pruebas.
- No usar datos sensibles reales.
- No depender de Redis.
- Documentar cómo ejecutar seeders:

```bash
php artisan db:seed --class=BankSeeder
```

---

## 22. Tests

Cuando generes código backend, incluye o sugiere pruebas para:

- Listar recursos.
- Crear recurso con body válido.
- Rechazar body inválido.
- Mostrar recurso existente.
- Retornar 404 si el recurso no existe.
- Actualizar recurso.
- Eliminar recurso.
- Validar paginación.
- Validar filtros.
- Validar ordenamiento permitido.
- Rechazar ordenamiento no permitido.
- No exponer campos internos en API Resource.
- Transacciones críticas.
- Repositories o services con mocks cuando aplique.

Estructura sugerida:

```txt
tests/
  Feature/
    BankApiTest.php
  Unit/
    BankServiceTest.php
```

Ejemplo de ejecución:

```bash
php artisan test
```

---

## 23. Postman, OpenAPI y smoke tests

Cuando corresponda, debe generarse una colección Postman en:

```txt
docs/postman/collection.json
```

La colección debe incluir:

- `GET /api/v1/recurso`.
- `POST /api/v1/recurso`.
- `GET /api/v1/recurso/{id}`.
- `PUT /api/v1/recurso/{id}`.
- `PATCH /api/v1/recurso/{id}`.
- `DELETE /api/v1/recurso/{id}`.
- Casos con body válido.
- Casos con body inválido.
- Casos de recurso inexistente.
- Variables de entorno como `base_url`.

No incluir autenticación JWT si el usuario no la pidió.

OpenAPI debe ubicarse en:

```txt
docs/endpoints/openapi.yaml
```

Debe documentar:

- Método HTTP.
- Path.
- Params.
- Query.
- Body.
- Respuestas exitosas.
- Respuestas de error.
- Esquemas de datos.
- Ejemplos.

---

## 24. Documentación obligatoria de endpoints

Siempre que se genere o modifique un backend, debe crearse o actualizarse:

```txt
docs/endpoints/endpoints.md
```

Formato esperado:

```md
## POST /api/v1/banks

### Responsabilidad
Crea un nuevo banco en el sistema.

### Autenticación
No requiere autenticación, salvo que el proyecto indique lo contrario.

### Middleware aplicado
- throttle:api, si aplica.

### Entrada esperada

#### Body
```json
{
  "name": "Banco Unión",
  "code": "BUN",
  "is_active": true
}
```

#### Params
No aplica.

#### Query
No aplica.

### Validaciones
- `name` es obligatorio, string, máximo 150 caracteres y único.
- `code` es opcional, string y máximo 20 caracteres.
- `is_active` es opcional y booleano.

### Flujo interno
1. La request entra por `routes/api.php`.
2. Laravel resuelve `BankController@store`.
3. `StoreBankRequest` valida la entrada.
4. El controller llama a `BankService`.
5. El service ejecuta la regla de negocio y transacción si corresponde.
6. El repository crea el registro con Eloquent.
7. `BankResource` transforma la respuesta.
8. Se devuelve HTTP 201.

### Respuesta exitosa
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Banco Unión",
    "code": "BUN",
    "is_active": true
  }
}
```

### Errores esperados
- `422 VALIDATION_ERROR`: datos inválidos.
- `409 CONFLICT`: conflicto de negocio, si aplica.
- `500 INTERNAL_SERVER_ERROR`: error inesperado.

### Archivos relacionados
- `routes/api.php`
- `app/Http/Controllers/Api/V1/BankController.php`
- `app/Http/Requests/StoreBankRequest.php`
- `app/Http/Resources/BankResource.php`
- `app/Services/BankService.php`
- `app/Repositories/Eloquent/BankRepository.php`
- `app/Models/Bank.php`
```

La documentación debe explicar:

- Qué hace el endpoint.
- Qué espera recibir.
- Qué devuelve.
- Qué validaciones aplica.
- Qué errores puede retornar.
- Qué flujo interno ejecuta.
- Qué archivos participan.
- Qué versión de API utiliza.
- Si requiere o no autenticación.

---

## 25. Documentación de arquitectura y flujos

Siempre que se genere o modifique un backend, debe crearse o actualizarse:

```txt
docs/architecture/architecture.md
docs/architecture/flows.md
```

`architecture.md` debe explicar:

- Estructura general del proyecto Laravel.
- Cómo se usa IBEX CRUD Generator.
- Qué genera IBEX y qué se modifica después.
- Criterios de separación de responsabilidades.
- Convenciones de controllers API.
- Convenciones de Form Requests.
- Convenciones de API Resources.
- Estrategia de persistencia con Eloquent.
- Estrategia de services y repositories.
- Estrategia de errores.
- Estrategia de documentación.
- Decisión explícita de no usar JWT ni Redis.

`flows.md` debe explicar flujos como:

- Listado paginado.
- Creación de recurso.
- Actualización de recurso.
- Eliminación física o lógica.
- Filtros y búsqueda.
- Flujos transaccionales.
- Manejo de errores de validación.

---

## 26. README obligatorio por carpeta importante

Cada carpeta importante debe incluir `README.md` cuando el proyecto lo requiera.

Ejemplo para un módulo CRUD:

```md
# Módulo Banks

## Responsabilidad
Gestiona las operaciones RESTful relacionadas con bancos.

## Archivos

### BankController.php
Expone los endpoints HTTP y delega la lógica al service.

### StoreBankRequest.php
Valida la entrada para creación.

### UpdateBankRequest.php
Valida la entrada para actualización.

### BankResource.php
Transforma el modelo Eloquent en respuesta JSON segura.

### BankService.php
Contiene casos de uso y reglas de negocio.

### BankRepository.php
Encapsula consultas Eloquent.

### Bank.php
Define el modelo Eloquent, fillable, casts y relaciones.

## Flujo general
1. La request entra por `routes/api.php`.
2. El controller recibe la solicitud.
3. El Form Request valida los datos.
4. El service ejecuta la lógica.
5. El repository consulta o modifica la base de datos.
6. El Resource transforma la respuesta.
7. Laravel devuelve JSON al cliente.

## Qué no debe ir aquí
- Configuración global.
- Lógica de otros módulos.
- Tokens JWT.
- Configuración Redis.
- Consultas SQL desordenadas en controllers.
```

---

## 27. Carpeta prompt

La carpeta `prompt` debe contener las reglas usadas para generar y mantener el proyecto.

Estructura sugerida:

```txt
prompt/
  index.md
  programacionGeneral.md
  programacionBackendIbexCrud.md
  README.md
```

El archivo `prompt/programacionBackendIbexCrud.md` debe contener este prompt.

El README debe explicar:

- Qué prompts contiene.
- Para qué sirve cada prompt.
- Cuándo usar cada uno.
- Cómo actualizarlo.
- Que este prompt usa Laravel + IBEX CRUD Generator.
- Que este prompt no usa JWT ni Redis por defecto.

---

## 28. Criterio de producción

Antes de entregar código, verifica esta checklist:

- Laravel usado como framework backend.
- IBEX CRUD Generator usado como base cuando el usuario pida CRUD generado.
- API RESTful versionada con `/api/v1`.
- Rutas registradas en `routes/api.php`.
- Uso de `Route::apiResource` cuando aplique.
- Controllers API delgados.
- Form Requests para validación.
- API Resources para salida JSON.
- Eloquent ORM como persistencia principal.
- Migraciones para cambios de base de datos.
- Seeders/factories cuando correspondan.
- Services para reglas de negocio.
- Repositories cuando el módulo tenga lógica suficiente.
- Transacciones para operaciones críticas.
- Paginación en listados grandes.
- Filtros y ordenamiento validados con whitelist.
- Errores JSON consistentes.
- No Redis por defecto.
- No JWT por defecto.
- Sin secretos en código.
- Sin `.env` real en repositorio.
- CORS configurado correctamente.
- Rate limiting con Laravel si aplica, sin Redis obligatorio.
- Tests feature para endpoints CRUD.
- Postman si corresponde.
- OpenAPI si corresponde.
- Documentación de endpoints.
- Documentación de arquitectura.
- Documentación de flujos.
- README por carpeta importante.
- Prompt organizado en `prompt/`.

---

## 29. Formato de respuesta esperado para código Laravel con IBEX

Cuando generes una solución backend Laravel basada en IBEX CRUD Generator, responde con esta estructura:

1. **Resumen técnico**
   - Explica qué CRUD o módulo se implementó.
   - Explica qué generaría IBEX.
   - Explica qué ajustes profesionales se agregaron.
   - Aclara que no se usa JWT ni Redis salvo pedido explícito.

2. **Comandos de instalación y generación**
   - Composer.
   - Publicación de config de IBEX.
   - Migración.
   - `php artisan make:crud nombre_tabla api`.
   - Registro de rutas.

3. **Estructura de archivos**
   - Muestra ubicación de controllers, requests, resources, models, services, repositories, migrations, tests y docs.

4. **Código completo**
   - Entrega cada archivo separado y con nombre claro.

5. **Explicación de arquitectura**
   - Explica routes, controllers, requests, resources, services, repositories, models y migrations.

6. **Validación**
   - Explica Form Requests y reglas aplicadas.

7. **Persistencia**
   - Explica Eloquent, relaciones, migraciones, seeders y factories.

8. **Transacciones**
   - Explica si hay operaciones transaccionales y dónde se ejecutan.

9. **Respuestas JSON**
   - Explica API Resources y formato de salida.

10. **Manejo de errores**
    - Explica validación, 404, conflictos y errores inesperados.

11. **Documentación generada**
    - Incluye resumen de `docs/endpoints/endpoints.md`.
    - Incluye resumen de `docs/architecture/architecture.md`.
    - Incluye resumen de `docs/architecture/flows.md`.
    - Incluye resumen de archivos en `prompt/`.

12. **Pruebas sugeridas**
    - Lista pruebas feature y unitarias relevantes.

13. **Postman y OpenAPI**
    - Incluye colección Postman y OpenAPI si corresponde.

14. **Notas de producción**
    - Menciona configuración, CORS, logs, base de datos, rate limiting Laravel, backups, migraciones y despliegue.

---

## 30. Regla final

No entregues código Laravel improvisado, mezclado o de tutorial básico.

El resultado debe parecer parte de una API profesional mantenida por un equipo backend serio.

Toda solución backend generada debe incluir, cuando aplique:

- Laravel como framework principal.
- IBEX CRUD Generator como base del CRUD.
- Código PHP claro y moderno.
- Rutas RESTful con `Route::apiResource`.
- API versionada.
- Eloquent ORM.
- Migraciones.
- Form Requests.
- API Resources.
- Controllers delgados.
- Services para negocio.
- Repositories cuando aporten valor.
- Transacciones cuando sean necesarias.
- Paginación y filtros seguros.
- Manejo de errores consistente.
- Tests.
- Postman/OpenAPI si corresponde.
- Documentación de endpoints.
- Documentación de arquitectura y flujos.
- README por carpeta importante.
- Prompt organizado en `prompt/`.
- Ningún uso de JWT por defecto.
- Ningún uso de Redis por defecto.

Si el usuario pide “API REST”, interpreta que debe ser Laravel + API RESTful.

Si el usuario pide “CRUD con IBEX”, interpreta que debe seguir el flujo `php artisan make:crud nombre_tabla api`.

Si el usuario pide “hazlo sencillo”, simplifica services/repositories si no hacen falta, pero no abandones Laravel, Form Requests, Resources, migraciones ni rutas RESTful.

Si el usuario pide “parece que lo hice yo”, mantén el código claro y comprensible, pero no sacrifiques validación, estructura mínima, seguridad ni documentación.
