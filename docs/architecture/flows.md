# Flujos principales

## Registro de cuenta local

1. El usuario envía `external_user_ref`, correo institucional opcional y contraseña.
2. `AuthService` valida el usuario contra `DirectorioUniversitarioGatewayInterface`.
3. Si el usuario existe externamente, se crea o reutiliza el registro `usuarios`.
4. Se crea `cuentas_usuario` con `password_hash`.
5. Se asigna el rol `ESTUDIANTE`.
6. Se emite token Sanctum.

## Inicio de sesión

1. El usuario envía correo institucional y contraseña.
2. Se busca `usuarios` y su `cuenta`.
3. Se verifica el hash de contraseña.
4. Se valida estado activo de usuario y cuenta.
5. Se actualiza `ultimo_acceso`.
6. Se emite token Sanctum.

## Inscripción a ayudantía

1. Se recibe `usuario_id` y `sesion_ayudantia_id`.
2. Se bloquea la sesión para evitar sobrecupo en concurrencia.
3. Se valida que la sesión esté `PROGRAMADA`.
4. Se valida que no exista inscripción activa duplicada.
5. Si hay cupo, estado `INSCRITO`.
6. Si no hay cupo, estado `EN_ESPERA`.

## Cancelación de inscripción

1. Se recibe el id de inscripción.
2. Solo se permite cancelar estados `INSCRITO` o `EN_ESPERA`.
3. Se actualiza a `CANCELADO`.

## Registro de asistencia

1. El coordinador envía `asistencia=true|false`.
2. Solo se permite sobre inscripción `INSCRITO`.
3. Si asistencia es true, estado `ASISTIO`.
4. Si asistencia es false, estado `NO_ASISTIO`.

## Postulación como auxiliar

1. El usuario envía materia, motivo, experiencia y disponibilidad por endpoints separados.
2. Se crea `postulaciones_auxiliar` en estado `PENDIENTE`.
3. El coordinador aprueba o rechaza.
4. Si aprueba:
   - La postulación pasa a `APROBADA`.
   - Se crea `auxiliares_materia`.
   - Se asigna rol `AUXILIAR` si no lo tenía.
5. Si rechaza, pasa a `RECHAZADA`.

## Programación de sesión

1. El coordinador envía oferta, fecha, hora y aula externa.
2. `SesionAyudantiaService` consulta disponibilidad con `AulaGatewayInterface`.
3. Si el aula está disponible, reserva el aula.
4. Se crea `sesiones_ayudantia` en estado `PROGRAMADA`.

## Estados

- Oferta: `BORRADOR`, `PUBLICADA`, `CERRADA`, `CANCELADA`.
- Sesión: `PROGRAMADA`, `EN_CURSO`, `FINALIZADA`, `CANCELADA`.
- Inscripción: `INSCRITO`, `EN_ESPERA`, `CANCELADO`, `ASISTIO`, `NO_ASISTIO`.
- Postulación: `PENDIENTE`, `APROBADA`, `RECHAZADA`, `CANCELADA`.
- AuxiliarMateria: `ACTIVO`, `INACTIVO`.
