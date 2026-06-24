# Auditoría de documentación y pruebas

## Estado actual

Se revisó la documentación y los artefactos de prueba para que coincidan con el contrato actual de la API:

- Registro de usuarios 100% interno.
- Sin consulta a servidor externo de directorio de usuarios.
- Postman alineado con `routes/api.php`.
- Smoke test completo con payloads reales.

## READMEs revisados

| Archivo | Estado |
|---|---|
| `README.md` | Actualizado con flujo de instalación, smoke completo y Postman. |
| `docs/postman/README.md` | Reescrito para explicar variables, ejecución y errores comunes. |
| `docs/testing/smoke-test.md` | Reescrito para reflejar cobertura completa. |
| `docs/endpoints/README.md` | Mantiene navegación hacia endpoints y route map. |
| `docs/endpoints/endpoints.md` | Ampliado con cobertura de Postman y payloads. |
| `docs/endpoints/route-map.md` | Actualizado con 82 rutas únicas y 102 requests. |
| `app/*/README.md` | Se mantienen como documentación de capa técnica. |
| `database/*/README.md` | Se mantienen como documentación de migraciones y seeders. |

## Resultado de auditoría técnica

```txt
PHP_LINT_OK
POSTMAN_JSON_OK
POSTMAN_ROUTE_AUDIT_OK
POSTMAN_UNIQUE_ROUTES=82
POSTMAN_REQUESTS=102
```

## Nota de uso

Para validar desde Postman, importa:

```txt
docs/postman/collection.json
docs/postman/EduBridgeBackend.local.environment.json
```

Ejecuta la colección completa desde Collection Runner en orden.
