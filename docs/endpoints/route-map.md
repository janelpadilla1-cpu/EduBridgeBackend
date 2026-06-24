# Route map real

Base local completa:

```txt
http://127.0.0.1:8000/api/v1
```

En Postman usa solo:

```txt
base_url = http://127.0.0.1:8000
```

La colección ya agrega `/api/v1`.

## Público

```txt
GET    /api/v1/catalogos/estados
POST   /api/v1/auth/register
POST   /api/v1/auth/login
```

## Auth protegido

```txt
GET    /api/v1/auth/me
POST   /api/v1/auth/logout
```

## CRUD protegido

Cada recurso CRUD tiene estas rutas:

```txt
GET    /api/v1/{recurso}
POST   /api/v1/{recurso}
GET    /api/v1/{recurso}/{id}
PUT    /api/v1/{recurso}/{id}
PATCH  /api/v1/{recurso}/{id}
DELETE /api/v1/{recurso}/{id}
```

Recursos:

```txt
usuarios
cuentas-usuario
roles-usuario
usuarios-roles
materias
ofertas-ayudantia
sesiones-ayudantia
inscripciones-ayudantia
postulaciones-auxiliar
auxiliares-materia
disponibilidad-auxiliar
```

## Acciones de negocio protegidas

```txt
POST   /api/v1/ofertas-ayudantia/{id}/publicar
POST   /api/v1/ofertas-ayudantia/{id}/cerrar
POST   /api/v1/ofertas-ayudantia/{id}/cancelar
POST   /api/v1/sesiones-ayudantia/{id}/iniciar
POST   /api/v1/sesiones-ayudantia/{id}/finalizar
POST   /api/v1/sesiones-ayudantia/{id}/cancelar
POST   /api/v1/inscripciones-ayudantia/{id}/cancelar
PATCH  /api/v1/inscripciones-ayudantia/{id}/asistencia
POST   /api/v1/postulaciones-auxiliar/{id}/aprobar
POST   /api/v1/postulaciones-auxiliar/{id}/rechazar
POST   /api/v1/postulaciones-auxiliar/{id}/cancelar
```

## Cobertura Postman actual

La colección `docs/postman/collection.json` cubre:

```txt
82 rutas únicas
102 requests de ejecución
0 rutas fuera de routes/api.php
```

Tiene más requests que rutas porque algunos endpoints necesitan registros separados para probar estados de negocio distintos: aprobar, rechazar, cancelar y eliminar.
