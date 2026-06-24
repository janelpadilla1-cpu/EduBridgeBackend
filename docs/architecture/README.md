# Documentación de arquitectura

Esta carpeta explica cómo está organizado EduBridgeBackend y cuáles son las decisiones técnicas vigentes.

## Archivos

| Archivo | Uso |
|---|---|
| `architecture.md` | Visión técnica, módulos, capas y decisiones de diseño. |
| `flows.md` | Flujos principales de negocio. |

## Cambio importante

El registro de usuarios ahora es **100% interno**. Ya no se consulta un directorio universitario externo para crear cuentas. El body de registro debe incluir los datos del usuario y su `rol` inicial.
