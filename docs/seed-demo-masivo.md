# Seed demo masivo EduBridge

Este seed carga una base de prueba grande para validar el frontend con flujo real y mucho contenido.

## Comando recomendado

```bash
php artisan migrate:fresh --seed
```

Si ya tienes las migraciones ejecutadas y solo quieres cargar o actualizar datos demo:

```bash
php artisan db:seed --class=EduBridgeDemoSeeder
```

## Credenciales

Todos los usuarios demo usan la misma contraseña:

```text
Password123!
```

Usuarios principales:

| Rol | Correo | Contraseña |
|---|---|---|
| ADMINISTRADOR | admin@edubridge.test | Password123! |
| COORDINADOR | coordinador@edubridge.test | Password123! |
| COORDINADOR | operaciones@edubridge.test | Password123! |
| ESTUDIANTE | estudiante@edubridge.test | Password123! |
| AUXILIAR | auxiliar001@edubridge.test | Password123! |
| AUXILIAR | auxiliar002@edubridge.test | Password123! |
| AUXILIAR | auxiliar003@edubridge.test | Password123! |

También se generan usuarios masivos:

```text
estudiante001@edubridge.test
estudiante002@edubridge.test
...
auxiliar001@edubridge.test
auxiliar002@edubridge.test
...
```

## Cantidad de datos por defecto

Con la configuración por defecto carga aproximadamente:

- 4 roles.
- 1 administrador.
- 2 coordinadores.
- 24 auxiliares.
- 160 estudiantes generados + 1 estudiante demo principal.
- 32 materias activas.
- 132 ofertas de ayudantía aproximadamente.
- 399 sesiones aproximadamente.
- Cientos de postulaciones de auxiliar.
- Cientos de asignaciones auxiliar-materia y disponibilidades.
- Miles de inscripciones distribuidas entre `INSCRITO`, `EN_ESPERA`, `CANCELADO`, `ASISTIO` y `NO_ASISTIO`.

## Variables opcionales en `.env`

Puedes aumentar o reducir el tamaño del seed sin tocar código:

```env
EDUBRIDGE_SEED_STUDENTS=160
EDUBRIDGE_SEED_AUXILIARIES=24
EDUBRIDGE_SEED_OFFERS_PER_SUBJECT=4
EDUBRIDGE_SEED_SESSIONS_PER_OFFER=3
```

Ejemplo para una base todavía más grande:

```env
EDUBRIDGE_SEED_STUDENTS=300
EDUBRIDGE_SEED_AUXILIARIES=40
EDUBRIDGE_SEED_OFFERS_PER_SUBJECT=5
EDUBRIDGE_SEED_SESSIONS_PER_OFFER=4
```

## Qué puedes probar en frontend

Con `estudiante@edubridge.test` puedes probar:

1. Login con token real.
2. Listado amplio de ofertas publicadas.
3. Detalle de oferta.
4. Pestaña/lista de sesiones.
5. Inscripción a sesiones programadas.
6. Desinscripción/cancelación de inscripción.
7. Visualización de sesiones llenas o con lista de espera.
8. Sesiones finalizadas con asistencia.
9. Ofertas en varios estados para filtros administrativos.
10. Postulaciones en estados `APROBADA`, `PENDIENTE`, `RECHAZADA` y `CANCELADA`.

## Nota importante

El seed es idempotente usando `updateOrCreate`, pero para una base limpia y consistente se recomienda usar:

```bash
php artisan migrate:fresh --seed
```
