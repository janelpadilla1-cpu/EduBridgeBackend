# EduBridgeBackend

API RESTful en Laravel para el **Sistema de Ayudantías Universitarias**. El proyecto fue generado siguiendo los diagramas PlantUML, el DDL PostgreSQL y los prompts de `prompt/`.

## Stack aplicado

- Laravel API RESTful.
- PHP 8.3+.
- Eloquent ORM.
- Form Requests para validación.
- API Resources para respuestas.
- Services y Repositories para separar reglas de negocio y persistencia.
- Laravel Sanctum para autenticación de API sin JWT.
- PostgreSQL como base recomendada.
- Cache, sesiones y colas sin Redis por defecto.
- Estructura compatible con el flujo de IBEX CRUD Generator.

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Instalación de IBEX CRUD Generator

El paquete queda en dependencias de desarrollo. Si se desea regenerar un CRUD base con IBEX:

```bash
composer require ibex/crud-generator --dev
php artisan vendor:publish --tag=crud
php artisan make:crud materias api
```

Después de generar con IBEX, mantener la separación usada en este proyecto: Controller delgado, Form Requests, API Resources, Service y Repository cuando exista lógica de negocio.

## Documentación útil

- Endpoints: `docs/endpoints/endpoints.md`
- OpenAPI: `docs/endpoints/openapi.yaml`
- Arquitectura: `docs/architecture/architecture.md`
- Flujos: `docs/architecture/flows.md`
- Postman: `docs/postman/collection.json`
- Diagramas: `docs/systemInfo/`
- DDL base: `docs/db/ddl.sql`

## Módulos principales

- Autenticación y cuenta local.
- Usuarios y roles.
- Materias.
- Ofertas de ayudantía.
- Sesiones de ayudantía.
- Inscripciones.
- Postulaciones a auxiliar.
- Asignación de auxiliares.
- Disponibilidad de auxiliares.
- Gateways externos para directorio universitario y aulas.

## Notas de producción

Los gateways externos incluidos son implementaciones fake/log para desarrollo. Para producción deben reemplazarse por implementaciones HTTP reales contra el directorio universitario, sistema de aulas y servicio de notificaciones.

No se generó Redis ni JWT. La autenticación usa Sanctum, que emite tokens opacos propios de Laravel y no tokens JWT.
