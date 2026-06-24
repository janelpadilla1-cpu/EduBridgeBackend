# Gateways

Esta carpeta contiene puertos hacia servicios externos o simulados.

## Alcance vigente

El registro de usuarios **no consulta un directorio externo**. Los datos del usuario se reciben desde el endpoint `POST /api/v1/auth/register` y se guardan internamente en `usuarios`, `cuentas_usuario` y `usuarios_roles`.

## Gateways incluidos

| Gateway | Interfaz | Implementación local | Uso |
|---|---|---|---|
| Aulas | `AulaGatewayInterface` | `FakeAulaGateway` | Consulta y reserva aulas para sesiones |

## Variables relacionadas

```env
AULA_GATEWAY=fake
```

## Regla de mantenimiento

Los servicios de negocio no deben llamar APIs externas directamente. Si se integra un proveedor real de aulas o notificaciones, debe encapsularse detrás de una interfaz y registrarse en `GatewayServiceProvider`.
