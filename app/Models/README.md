# Models

Modelos Eloquent del dominio EduBridge.

## Decisión clave

Todos los modelos principales usan UUID como llave primaria:

```php
use HasUuids;
protected $keyType = 'string';
public $incrementing = false;
```

## Modelos principales

| Modelo | Tabla |
|---|---|
| `Usuario` | `usuarios` |
| `CuentaUsuario` | `cuentas_usuario` |
| `RolUsuario` | `roles_usuario` |
| `UsuarioRol` | `usuarios_roles` |
| `Materia` | `materias` |
| `OfertaAyudantia` | `ofertas_ayudantia` |
| `SesionAyudantia` | `sesiones_ayudantia` |
| `InscripcionAyudantia` | `inscripciones_ayudantia` |
| `PostulacionAuxiliar` | `postulaciones_auxiliar` |
| `AuxiliarMateria` | `auxiliares_materia` |
| `DisponibilidadAuxiliar` | `disponibilidad_auxiliar` |

## Sanctum y UUID

Como `Usuario` usa UUID, la migración de Sanctum debe usar:

```php
$table->uuidMorphs('tokenable');
```

No uses `$table->morphs('tokenable')` porque crea `tokenable_id` numérico y rompe el registro/login.

## Usuarios

`external_user_ref` se conserva como referencia técnica interna. El registro ya no depende de un directorio externo; el usuario se crea con los datos enviados al endpoint de registro.
