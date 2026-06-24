# Form Requests

Validaciones de entrada para la API.

## Objetivo

Centralizar reglas de validación para que los controladores no tengan lógica repetida.

## Tipos de requests

| Prefijo | Uso |
|---|---|
| `Store*Request` | Validación para crear registros |
| `Update*Request` | Validación para actualizar registros |
| `List*Request` | Filtros de listados paginados |
| `RegisterRequest` | Registro interno de usuario con `correo_institucional`, `nombre_completo`, `rol` y contraseña |
| `LoginRequest` | Inicio de sesión |
| `RegistrarAsistenciaRequest` | Registro de asistencia |

## Parámetros comunes de listado

```txt
search
page
per_page
sort_by
sort_order
```

## Regla de mantenimiento

Toda validación de entrada nueva debe agregarse aquí, no dentro del controller.

## Registro interno

`RegisterRequest` no valida contra servicios externos. Normaliza el correo, genera `external_user_ref` desde el correo cuando no se envía y restringe `rol` a `ESTUDIANTE`, `AUXILIAR`, `COORDINADOR` o `ADMINISTRADOR`.
