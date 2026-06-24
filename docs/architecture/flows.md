# Flujos principales

## Registro de cuenta local interna

1. El usuario envía `correo_institucional`, `nombre_completo`, `rol`, contraseña y, opcionalmente, `codigo_universitario` y `external_user_ref`.
2. `RegisterRequest` normaliza correo y rol, y valida que el rol sea uno de: `ESTUDIANTE`, `AUXILIAR`, `COORDINADOR` o `ADMINISTRADOR`.
3. `AuthService` crea el registro en `usuarios` usando únicamente la información enviada al backend.
4. Se crea `cuentas_usuario` con `password_hash`.
5. Se busca o crea el rol local en `roles_usuario`.
6. Se crea la relación en `usuarios_roles` con UUID propio.
7. Se emite token Sanctum.

> Decisión vigente: el registro ya no consulta `gateway de directorio de usuarios` ni ningún servidor externo de usuarios. Todo el alta de usuario es interna.

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

1. El coordinador envía oferta, fecha, hora y aula.
2. `SesionAyudantiaService` consulta disponibilidad con `AulaGatewayInterface`.
3. En desarrollo local se usa `FakeAulaGateway`.
4. Si el aula está disponible, se reserva.
5. Se crea `sesiones_ayudantia` en estado `PROGRAMADA`.

## Estados

- Oferta: `BORRADOR`, `PUBLICADA`, `CERRADA`, `CANCELADA`.
- Sesión: `PROGRAMADA`, `EN_CURSO`, `FINALIZADA`, `CANCELADA`.
- Inscripción: `INSCRITO`, `EN_ESPERA`, `CANCELADO`, `ASISTIO`, `NO_ASISTIO`.
- Postulación: `PENDIENTE`, `APROBADA`, `RECHAZADA`, `CANCELADA`.
- AuxiliarMateria: `ACTIVO`, `INACTIVO`.
