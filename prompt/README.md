# Prompt

Reglas y contexto usados para generar y mantener EduBridgeBackend.

## Orden de lectura recomendado

1. `index.md`
2. `programacionGeneral.md`
3. `programacionBackend.md`
4. `contextSystem.md`

## Propósito

Estos archivos documentan el criterio de generación del proyecto: Laravel, RESTful API, estructura tipo IBEX CRUD Generator, separación por capas, validación con Form Requests y autenticación con Sanctum.

## Nota

Si el backend cambia de stack o se agregan nuevos requisitos obligatorios, actualiza primero estos prompts para mantener consistencia en futuras generaciones.

## Alcance vigente

El prompt fue actualizado para que el registro de usuarios sea interno. No se debe generar código que consulte `DirectorioUniversitarioGateway` ni APIs externas para obtener datos del usuario al registrarse.
