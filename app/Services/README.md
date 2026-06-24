# Services

Capa de casos de uso y reglas de negocio.

## Objetivo

Coordinar transacciones, validar reglas de negocio y llamar a repositorios/gateways cuando corresponda.

## Ejemplos de reglas ubicadas aquí

- Registro interno de cuenta local sin directorio externo.
- Emisión de tokens Sanctum.
- Publicar, cerrar o cancelar ofertas.
- Iniciar, finalizar o cancelar sesiones.
- Inscribir usuarios a sesiones.
- Registrar asistencia.
- Aprobar postulaciones y asignar rol de auxiliar.

## Regla de mantenimiento

Si una acción cambia estado o coordina más de una tabla, debe vivir aquí y ejecutarse dentro de una transacción.
