# API Resources

Transforman modelos Eloquent en respuestas JSON estables.

## Objetivo

Evitar exponer directamente todos los campos internos de la base de datos.

## Convención de respuesta

Los endpoints CRUD devuelven recursos en formato Laravel:

```json
{
  "data": {
    "id": "uuid",
    "campo": "valor"
  }
}
```

Los listados devuelven colección paginada con `data`, `links` y `meta`.

## Regla de mantenimiento

Cuando agregues campos sensibles al modelo, decide explícitamente si deben aparecer o no en el Resource.
