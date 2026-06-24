# Endpoints

Documentación de rutas reales de la API.

## Archivos

| Archivo | Uso |
|---|---|
| `endpoints.md` | Mapa legible de endpoints |
| `openapi.yaml` | Contrato OpenAPI simplificado |
| `route-map.md` | Lista compacta de rutas usada para revisar Postman |

## Base local

```txt
http://127.0.0.1:8000/api/v1
```

## Importante

En Postman, `base_url` debe ser:

```txt
http://127.0.0.1:8000
```

No debe incluir `/api`, porque las rutas de la colección ya tienen `/api/v1`.

## Registro interno

`POST /api/v1/auth/register` no llama a servidores externos. Crea usuario, cuenta y relación usuario/rol dentro de la base local.
