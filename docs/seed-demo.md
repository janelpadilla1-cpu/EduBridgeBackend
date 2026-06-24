# Seed demo EduBridge

Este seed carga una base de prueba amplia para poder ver contenido real desde el frontend de EduBridge.

## Comando recomendado

```bash
php artisan migrate:fresh --seed
```

Si ya migraste y solo quieres volver a ejecutar datos demo:

```bash
php artisan db:seed --class=EduBridgeDemoSeeder
```

## Credenciales de prueba

Todos los usuarios demo usan la misma contraseña:

```text
Password123!
```

Usuarios principales:

| Rol | Correo | Contraseña |
|---|---|---|
| ADMINISTRADOR | admin@edubridge.test | Password123! |
| COORDINADOR | coordinador@edubridge.test | Password123! |
| AUXILIAR | ana.quispe@edubridge.test | Password123! |
| AUXILIAR | luis.mamani@edubridge.test | Password123! |
| AUXILIAR | maria.aguilar@edubridge.test | Password123! |
| ESTUDIANTE | estudiante@edubridge.test | Password123! |

## Qué carga

- 4 roles: `ESTUDIANTE`, `AUXILIAR`, `COORDINADOR`, `ADMINISTRADOR`.
- 24 usuarios demo con cuenta y contraseña hasheada.
- 12 materias activas.
- 15 ofertas de ayudantía, principalmente `PUBLICADA`, más ejemplos en `BORRADOR`, `CERRADA` y `CANCELADA`.
- 22 sesiones de ayudantía con fechas dinámicas relativas al día de ejecución.
- Auxiliares asignados a materias.
- Disponibilidad semanal de auxiliares.
- Postulaciones en estados `APROBADA`, `PENDIENTE`, `RECHAZADA` y `CANCELADA`.
- Inscripciones en estados `INSCRITO`, `EN_ESPERA`, `CANCELADO`, `ASISTIO` y `NO_ASISTIO`.

## Nota para probar el flujo estudiante

Para validar el flujo del frontend, inicia sesión con:

```text
correo: estudiante@edubridge.test
password: Password123!
```

Ese usuario ya tiene una inscripción activa y una inscripción cancelada, pero todavía puede inscribirse a muchas sesiones `PROGRAMADA` disponibles. Esto permite probar:

1. Listado de ofertas de ayudantía publicadas.
2. Ver detalle de una oferta.
3. Ver sesiones disponibles.
4. Inscribirse a una sesión.
5. Cancelar/desinscribirse de una inscripción existente.
