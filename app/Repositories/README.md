# Repositories

Capa de persistencia del proyecto.

## Objetivo

Separar las consultas Eloquent de los servicios de negocio.

## Estructura

```txt
Contracts/   Interfaces
Eloquent/    Implementaciones con Eloquent
```

## Base repository

`BaseEloquentRepository` ya incluye:

- `query()`
- `paginate()`
- `findOrFail()`
- `create()`
- `update()`
- `delete()`

## Regla de mantenimiento

Si una consulta se vuelve específica del módulo, agrégala al contrato del repositorio correspondiente y no directamente al servicio.
