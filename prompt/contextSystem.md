# Contexto del sistema

## 1. Naturaleza del sistema

El sistema corresponde a una plataforma para organizar **ayudantías universitarias**, permitiendo que cualquier persona perteneciente a la universidad pueda consultar, registrarse y participar en clases de apoyo académico sin importar su facultad o carrera.

El objetivo principal del sistema es digitalizar, ordenar y controlar los procesos relacionados con:

* Registro de usuarios universitarios.
* Creación de cuentas locales mediante contraseña.
* Consulta de materias con ayudantías disponibles.
* Publicación de ofertas de ayudantía.
* Programación de sesiones de ayudantía.
* Visualización de horarios.
* Referencia desacoplada a aulas externas.
* Inscripción de estudiantes a sesiones.
* Postulación de usuarios como auxiliares.
* Asignación de auxiliares a materias.
* Registro de disponibilidad de auxiliares.
* Control de cupos.
* Control de asistencia.
* Trazabilidad básica de acciones relevantes.

El sistema no debe construirse como un simple CRUD de materias y horarios. Debe diseñarse como una solución académica organizada, desacoplada y preparada para integrarse con sistemas institucionales existentes.

## 2. Contexto institucional

La universidad necesita una aplicación para centralizar las ayudantías ofrecidas en distintas materias. Actualmente, los estudiantes pueden tener dificultades para conocer:

* Qué materias tienen ayudantías disponibles.
* En qué horarios se dictan.
* En qué aula se realizará cada sesión.
* Si todavía existen cupos disponibles.
* Cómo inscribirse a una ayudantía.
* Cómo postularse como auxiliar.
* Qué sesiones tiene asignadas un auxiliar.
* Qué disponibilidad horaria tienen los auxiliares.

El sistema debe permitir que cualquier persona de la universidad pueda acceder a las ayudantías, sin necesidad de validar facultad, carrera o pertenencia a una unidad académica específica.

Regla institucional importante:

```txt
Cualquier usuario universitario puede acceder a cualquier ayudantía disponible.
```

Por lo tanto, el sistema no debe guardar ni depender de facultades.

## 3. Alcance general del sistema

El sistema debe cubrir el flujo académico principal:

```txt
Usuario universitario
→ Creación de cuenta local
→ Inicio de sesión
→ Consulta de materias con ayudantías
→ Consulta de sesiones y horarios
→ Inscripción a una sesión
→ Participación o asistencia
```

También debe cubrir el flujo de auxiliares:

```txt
Usuario universitario
→ Postulación como auxiliar
→ Registro de disponibilidad
→ Revisión por coordinador
→ Aprobación
→ Asignación a materia
→ Programación de sesiones
```

El sistema debe ser suficientemente flexible para que una persona pueda comportarse como estudiante en una materia y como auxiliar en otra.

Regla central:

```txt
UN USUARIO PUEDE TENER MÁS DE UN ROL DENTRO DEL SISTEMA.
```

## 4. Enfoque arquitectónico

El sistema debe diseñarse como una aplicación backend modular, preparada para integrarse con servicios externos de la universidad.

Componentes sugeridos:

```txt
API Gateway
Módulo de Autenticación
Módulo de Usuarios
Módulo de Materias
Módulo de Ayudantías
Módulo de Inscripciones
Módulo de Auxiliares
Módulo de Disponibilidad
Módulo de Integración con Aulas (Simplificado, no necesito llamarlo a una API externa, solo simular una llamada)
Módulo de Integración con Directorio Universitario (Simplificado, no necesito llamarlo a una API externa, solo simular una llamada)
Módulo de Auditoría
```

La arquitectura debe evitar el acoplamiento directo con sistemas externos.

La comunicación con sistemas institucionales debe realizarse mediante adaptadores o gateways:

```txt
AulaGateway
DirectorioUniversitarioGateway
```

Estos gateways representan contratos de integración, no tablas internas.

## 5. Sistemas externos

El sistema debe integrarse con al menos dos fuentes externas:

### Sistema universitario de personas

Responsable de validar que una persona pertenece a la universidad.

El sistema de ayudantías no debe duplicar toda la información institucional de la persona. Solo debe conservar los datos mínimos necesarios para operar.

Referencia principal:

```txt
external_user_ref
```

Este campo representa el identificador externo del usuario en el sistema institucional.

### Sistema de aulas

Responsable de administrar aulas, disponibilidad y reservas.

El sistema de ayudantías no debe crear una tabla propia de aulas.

En su lugar, cada sesión de ayudantía debe guardar una referencia externa:

```txt
aula_ref_id
```

Opcionalmente puede guardar un nombre cacheado para visualización:

```txt
aula_nombre_cache
```

Esta información cacheada no debe considerarse la fuente oficial del aula.

## 6. Decisión sobre facultades

El sistema no debe modelar facultades.

No deben existir entidades como:

```txt
Facultad
Carrera
Departamento Académico
```

La razón es que la regla de negocio indica que cualquier usuario universitario puede acceder a las ayudantías, independientemente de su facultad.

Incluir facultad puede generar restricciones innecesarias y acoplar el sistema a reglas institucionales que no forman parte del alcance actual.

## 7. Usuarios y cuentas

El sistema debe mantener la modelación de usuarios.

El usuario representa a cualquier persona universitaria que accede al sistema.

Un usuario puede:

* Consultar materias con ayudantías.
* Inscribirse a sesiones.
* Cancelar inscripciones.
* Postularse como auxiliar.
* Registrar disponibilidad.
* Ser asignado como auxiliar.
* Administrar ofertas si posee rol autorizado.

La cuenta de usuario debe estar separada del perfil del usuario.

Entidades principales:

```txt
Usuario
CuentaUsuario
RolUsuario
UsuarioRol
```

La creación de cuenta debe seguir esta lógica:

```txt
Usuario ingresa su identificador institucional
→ El sistema valida su existencia en el directorio universitario externo
→ El usuario registra únicamente su contraseña
→ El sistema crea su cuenta local
```

El usuario no debe registrar manualmente datos que ya existen en la universidad, como nombre completo o correo institucional. Estos datos deben obtenerse desde el sistema externo cuando sea posible.

## 8. Roles del sistema

El sistema debe manejar roles flexibles.

Roles sugeridos:

```txt
ESTUDIANTE
AUXILIAR
COORDINADOR
ADMINISTRADOR
```

Un usuario puede tener varios roles al mismo tiempo.

Ejemplo:

```txt
Un usuario puede ser ESTUDIANTE en Física I y AUXILIAR en Cálculo I.
```

Por eso se debe usar una relación intermedia:

```txt
UsuarioRol
```

No se recomienda guardar un único campo `rol` en la tabla de usuarios.

## 9. Materias

La materia representa una asignatura que puede tener ayudantías.

Debe contener:

* Código.
* Nombre.
* Descripción.
* Estado.

Ejemplo:

```txt
MAT101 - Cálculo I
FIS101 - Física I
QUI101 - Química General
```

La materia no debe depender de facultad.

Una materia puede tener varias ofertas de ayudantía a lo largo del tiempo.

## 10. Oferta de ayudantía

La oferta de ayudantía representa que una materia tiene apoyo académico disponible.

Debe contener:

* Materia asociada.
* Título.
* Descripción.
* Cupo máximo referencial.
* Estado.
* Fecha de creación.

Estados sugeridos:

```txt
BORRADOR
PUBLICADA
CERRADA
CANCELADA
```

Una oferta puede tener una o muchas sesiones programadas.

Ejemplo:

```txt
Oferta: Ayudantía de Cálculo I - Parcial 2
Sesión 1: Lunes 10:00 - 11:30
Sesión 2: Miércoles 15:00 - 16:30
```

## 11. Sesiones de ayudantía

La sesión de ayudantía representa una clase concreta.

Debe contener:

* Oferta de ayudantía asociada.
* Auxiliar asignado, si aplica.
* Fecha.
* Hora de inicio.
* Hora de fin.
* Referencia externa de aula.
* Nombre cacheado del aula, si aplica.
* Estado.

Estados sugeridos:

```txt
PROGRAMADA
EN_CURSO
FINALIZADA
CANCELADA
```

La sesión no debe tener una relación interna con una tabla `Aula`.

La regla debe ser:

```txt
El aula pertenece a un sistema externo.
La sesión solo guarda una referencia desacoplada.
```

Antes de programar una sesión, el sistema debe consultar disponibilidad al servicio externo de aulas.

## 12. Inscripciones

La inscripción representa que un usuario se registra en una sesión de ayudantía.

Debe contener:

* Usuario.
* Sesión de ayudantía.
* Estado.
* Fecha de inscripción.
* Asistencia.

Estados sugeridos:

```txt
INSCRITO
EN_ESPERA
CANCELADO
ASISTIO
NO_ASISTIO
```

Reglas principales:

1. Un usuario no puede inscribirse dos veces a la misma sesión.
2. Una sesión no debe superar el cupo definido.
3. Si no hay cupo, el sistema puede registrar al usuario en lista de espera.
4. El usuario puede cancelar su inscripción si la sesión aún no finalizó.
5. La asistencia puede registrarse posteriormente.

## 13. Postulación como auxiliar

Cualquier usuario universitario puede postularse como auxiliar.

La postulación debe estar asociada a una materia.

Debe contener:

* Usuario.
* Materia.
* Motivo.
* Experiencia.
* Estado.
* Fecha de postulación.

Estados sugeridos:

```txt
PENDIENTE
APROBADA
RECHAZADA
CANCELADA
```

El coordinador debe poder aprobar o rechazar postulaciones.

Cuando una postulación se aprueba, el sistema debe crear una relación de auxiliar con la materia.

## 14. Auxiliar por materia

La entidad `AuxiliarMateria` representa que un usuario fue aprobado como auxiliar para una materia específica.

Debe contener:

* Usuario.
* Materia.
* Estado.
* Fecha de asignación.

Regla importante:

```txt
Ser auxiliar de una materia no convierte automáticamente al usuario en auxiliar de todas las materias.
```

Ejemplo:

```txt
Juan puede ser auxiliar de Cálculo I, pero no de Física I.
```

Por eso la relación entre usuario y materia debe ser explícita.

## 15. Disponibilidad del auxiliar

La disponibilidad permite registrar en qué días y horarios un auxiliar puede dictar ayudantías.

Debe contener:

* Usuario.
* Día de la semana.
* Hora de inicio.
* Hora de fin.
* Estado.

Estados sugeridos:

```txt
ACTIVA
INACTIVA
CANCELADA
```

Reglas principales:

1. La hora de fin debe ser mayor que la hora de inicio.
2. Un auxiliar puede tener varias franjas de disponibilidad.
3. La disponibilidad sirve como apoyo para programar sesiones, pero no reemplaza la validación final del horario.

## 16. Modelo de datos principal

Entidades persistentes recomendadas:

```txt
Usuario
CuentaUsuario
RolUsuario
UsuarioRol
Materia
OfertaAyudantia
SesionAyudantia
InscripcionAyudantia
PostulacionAuxiliar
AuxiliarMateria
DisponibilidadAuxiliar
```

Elementos no persistentes del dominio:

```txt
AulaGateway
DirectorioUniversitarioGateway
AuthService
AyudantiaService
InscripcionService
AuxiliarService
```

Estos elementos representan lógica de aplicación o integración, no tablas de base de datos.

## 17. Relaciones principales

Relaciones del modelo:

```txt
Usuario 1 ── 1 CuentaUsuario
Usuario 1 ── N UsuarioRol
RolUsuario 1 ── N UsuarioRol

Materia 1 ── N OfertaAyudantia
OfertaAyudantia 1 ── N SesionAyudantia

Usuario 1 ── N InscripcionAyudantia
SesionAyudantia 1 ── N InscripcionAyudantia

Usuario 1 ── N PostulacionAuxiliar
Materia 1 ── N PostulacionAuxiliar

Usuario 1 ── N AuxiliarMateria
Materia 1 ── N AuxiliarMateria

Usuario 1 ── N DisponibilidadAuxiliar

Usuario 1 ── N SesionAyudantia como auxiliar asignado
```

La relación de `SesionAyudantia` con `Usuario` mediante `auxiliar_id` debe ser opcional, ya que puede existir una sesión programada sin auxiliar asignado inicialmente.

## 18. Reglas de negocio principales

El sistema debe cumplir las siguientes reglas:

1. Solo usuarios universitarios válidos pueden crear cuenta.
2. Al crear cuenta, el usuario solo registra contraseña.
3. Los datos institucionales básicos deben obtenerse desde el sistema externo.
4. El sistema debe mantener usuarios propios para manejar roles e inscripciones.
5. Un usuario puede tener múltiples roles.
6. No se debe guardar facultad.
7. Las materias no dependen de facultad.
8. Cualquier usuario universitario puede consultar cualquier ayudantía.
9. Cualquier usuario universitario puede inscribirse a una sesión disponible.
10. Un usuario no puede inscribirse dos veces a la misma sesión.
11. Una sesión debe respetar su cupo máximo.
12. Si no hay cupo, el sistema puede manejar lista de espera.
13. Las aulas no se administran dentro del sistema.
14. Las aulas se referencian mediante `aula_ref_id`.
15. La disponibilidad de aulas debe consultarse en un sistema externo.
16. Un usuario puede postularse como auxiliar para una materia.
17. La postulación debe ser aprobada antes de asignar al usuario como auxiliar.
18. Ser auxiliar de una materia no implica ser auxiliar de todas.
19. Las sesiones pueden tener auxiliar asignado o quedar pendientes de asignación.
20. La disponibilidad del auxiliar ayuda a programar sesiones.
21. Las acciones importantes deben dejar trazabilidad básica.
22. Los estados deben guardarse como texto para evitar acoplamiento fuerte a enums de base de datos.

## 19. Validaciones importantes

Validaciones recomendadas:

### Usuarios

* `external_user_ref` debe ser único.
* `correo_institucional` debe ser único.
* `codigo_universitario` debe ser único si existe.
* `password_hash` nunca debe guardar la contraseña en texto plano.

### Sesiones

* La hora de fin debe ser mayor que la hora de inicio.
* El aula debe existir o ser válida en el sistema externo.
* La sesión no debe cruzarse con otra sesión en la misma aula.
* La sesión no debe superar el cupo disponible.
* La fecha y horario deben ser coherentes con la disponibilidad del auxiliar cuando se asigne uno.

### Inscripciones

* Un usuario no puede duplicar inscripción en la misma sesión.
* No se puede inscribir a una sesión cancelada o finalizada.
* La asistencia solo debe registrarse cuando la sesión haya ocurrido o esté en curso.

### Auxiliares

* Un usuario no puede ser asignado dos veces como auxiliar de la misma materia.
* La postulación aprobada debe generar o habilitar la relación `AuxiliarMateria`.
* La disponibilidad debe tener hora final mayor que hora inicial.

## 20. Estados recomendados

Los estados deben almacenarse como texto.

No se debe usar `CREATE TYPE`, enums nativos ni tipos propios de base de datos.

Estados sugeridos:

### Usuario

```txt
ACTIVO
INACTIVO
BLOQUEADO
```

### Cuenta

```txt
ACTIVA
PENDIENTE
BLOQUEADA
```

### Materia

```txt
ACTIVA
INACTIVA
```

### Oferta de ayudantía

```txt
BORRADOR
PUBLICADA
CERRADA
CANCELADA
```

### Sesión de ayudantía

```txt
PROGRAMADA
EN_CURSO
FINALIZADA
CANCELADA
```

### Inscripción

```txt
INSCRITO
EN_ESPERA
CANCELADO
ASISTIO
NO_ASISTIO
```

### Postulación auxiliar

```txt
PENDIENTE
APROBADA
RECHAZADA
CANCELADA
```

### Auxiliar materia

```txt
ACTIVO
INACTIVO
SUSPENDIDO
```

### Disponibilidad auxiliar

```txt
ACTIVA
INACTIVA
CANCELADA
```

## 21. Seguridad

El sistema debe manejar autenticación y autorización.

Reglas de seguridad:

1. Las contraseñas deben almacenarse hasheadas.
2. No se debe guardar contraseña en texto plano.
3. Debe existir control de sesión mediante tokens.
4. Los roles deben controlar el acceso a las funcionalidades.
5. Un estudiante puede consultar e inscribirse.
6. Un auxiliar puede consultar sus sesiones asignadas y registrar disponibilidad.
7. Un coordinador puede crear ofertas, programar sesiones, asignar auxiliares y revisar postulaciones.
8. Un administrador puede gestionar usuarios, roles y configuración general.
9. Las acciones críticas deben quedar auditadas.
10. El sistema no debe confiar únicamente en datos enviados desde el frontend.

## 22. Auditoría y trazabilidad

El sistema debe registrar trazabilidad básica de acciones relevantes.

Acciones recomendadas para auditar:

* Creación de cuenta.
* Inicio de sesión.
* Creación de materia.
* Publicación de oferta de ayudantía.
* Programación de sesión.
* Cambio de aula.
* Cambio de horario.
* Inscripción a sesión.
* Cancelación de inscripción.
* Registro de asistencia.
* Postulación como auxiliar.
* Aprobación o rechazo de postulación.
* Asignación de auxiliar a materia.
* Cambio de estado de sesión.
* Cambio de estado de oferta.

La auditoría puede implementarse inicialmente con campos estándar:

```txt
created_at
updated_at
created_by
updated_by
```

Y posteriormente evolucionar hacia una tabla o servicio de auditoría.

## 23. Diagramas requeridos

El diseño debe incluir como mínimo:

```txt
1. Diagrama de clases.
2. Diagrama entidad-relación.
3. Diagrama de casos de uso.
4. Diagrama de actividad del proceso de inscripción.
5. Diagrama de actividad del proceso de postulación como auxiliar.
6. Diagrama de componentes.
7. Diagrama de secuencia para inscripción.
8. Diagrama de secuencia para programación de sesión.
```

Los diagramas deben reflejar que:

* Los usuarios se modelan dentro del sistema.
* Las aulas son referencias externas.
* El directorio universitario es un sistema externo.
* No existe entidad facultad.
* Los estados se manejan como texto.
* La arquitectura debe permanecer desacoplada.

## 24. Recomendación de módulos backend

Módulos backend sugeridos:

```txt
AuthModule
UsersModule
RolesModule
SubjectsModule
TutoringOffersModule
TutoringSessionsModule
EnrollmentsModule
AssistantApplicationsModule
AssistantsModule
AssistantAvailabilityModule
ClassroomIntegrationModule
UniversityDirectoryIntegrationModule
AuditModule
```

Nombres equivalentes en español:

```txt
ModuloAutenticacion
ModuloUsuarios
ModuloRoles
ModuloMaterias
ModuloOfertasAyudantia
ModuloSesionesAyudantia
ModuloInscripciones
ModuloPostulacionesAuxiliar
ModuloAuxiliares
ModuloDisponibilidadAuxiliar
ModuloIntegracionAulas
ModuloIntegracionDirectorioUniversitario
ModuloAuditoria
```

## 25. Endpoints REST sugeridos

Endpoints iniciales recomendados:

```txt
POST   /auth/register-password
POST   /auth/login

GET    /materias
POST   /materias
PATCH  /materias/{id}

GET    /ofertas-ayudantia
POST   /ofertas-ayudantia
PATCH  /ofertas-ayudantia/{id}

GET    /sesiones-ayudantia
POST   /sesiones-ayudantia
PATCH  /sesiones-ayudantia/{id}
DELETE /sesiones-ayudantia/{id}

POST   /inscripciones
PATCH  /inscripciones/{id}/cancelar
PATCH  /inscripciones/{id}/asistencia

POST   /postulaciones-auxiliar
GET    /postulaciones-auxiliar
PATCH  /postulaciones-auxiliar/{id}/aprobar
PATCH  /postulaciones-auxiliar/{id}/rechazar

GET    /auxiliares
POST   /auxiliares/asignar

GET    /disponibilidad-auxiliar
POST   /disponibilidad-auxiliar
PATCH  /disponibilidad-auxiliar/{id}
DELETE /disponibilidad-auxiliar/{id}

GET    /integraciones/aulas/disponibilidad
POST   /integraciones/aulas/reserva
```

Los endpoints de integración pueden cambiar según el contrato real de la universidad.

## 26. Calidad esperada

El sistema debe diseñarse como una solución académica real, mantenible y preparada para integrarse con infraestructura institucional.

Debe priorizar:

* Claridad del dominio.
* Bajo acoplamiento.
* Separación de responsabilidades.
* Seguridad.
* Trazabilidad.
* Facilidad de mantenimiento.
* Validaciones de negocio.
* Control de cupos.
* Gestión flexible de roles.
* Integración limpia con sistemas externos.
* Simplicidad para el estudiante.
* Facilidad de administración para coordinadores.

No debe construirse como un sistema cerrado ni excesivamente dependiente de estructuras internas de la universidad.

## 27. Criterio final

La solución debe diseñarse como un sistema de gestión de ayudantías universitarias desacoplado.

No debe depender de facultades ni duplicar sistemas existentes de aulas o personas.

Debe mantener como reglas centrales:

```txt
CUALQUIER USUARIO UNIVERSITARIO PUEDE ACCEDER A CUALQUIER AYUDANTÍA.
```

```txt
LAS AULAS SON REFERENCIAS EXTERNAS, NO ENTIDADES INTERNAS.
```

```txt
EL USUARIO SOLO REGISTRA CONTRASEÑA; SU IDENTIDAD SE VALIDA CONTRA EL SISTEMA UNIVERSITARIO.
```

```txt
UN USUARIO PUEDE SER ESTUDIANTE Y AUXILIAR AL MISMO TIEMPO.
```

La prioridad final del diseño debe ser:

1. Simplicidad para el usuario.
2. Bajo acoplamiento con sistemas externos.
3. Modelación clara de usuarios.
4. Gestión correcta de ayudantías y sesiones.
5. Control de cupos e inscripciones.
6. Gestión flexible de auxiliares.
7. Seguridad.
8. Trazabilidad.
9. Mantenibilidad.
10. Preparación para crecimiento futuro.
