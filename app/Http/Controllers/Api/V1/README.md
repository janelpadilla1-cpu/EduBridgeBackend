# Controllers API V1

Controladores RESTful del backend EduBridge.

## Responsabilidad

Los controladores deben ser delgados. Su trabajo es recibir el request validado, llamar al servicio correspondiente y devolver un `JsonResponse` o `Resource`.

## Qué no debe ir aquí

- Reglas de negocio complejas.
- Consultas Eloquent extensas.
- Validaciones manuales repetidas.
- Transformación grande de respuestas.

## Patrón usado

```txt
Request -> Controller -> Service -> Repository -> Model -> Resource
```

## Rutas reales

Las rutas se declaran en:

```txt
routes/api.php
```

Todas las rutas de esta carpeta están bajo:

```txt
/api/v1
```

Los CRUD protegidos usan `auth:sanctum`.
