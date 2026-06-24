# Migraciones

Migraciones Laravel para crear la estructura de base de datos de EduBridge.

## Base local recomendada

```txt
MySQL / MariaDB desde Laragon
```

## Decisiones importantes

- Llaves primarias UUID en tablas del dominio.
- `usuarios_roles` conserva `id` UUID porque se expone como CRUD.
- `personal_access_tokens` usa `uuidMorphs()` por compatibilidad con `Usuario` UUID.
- Algunas restricciones de negocio se refuerzan en Form Requests y Services.

## Comando recomendado en desarrollo

```bash
php artisan migrate:fresh --seed
```

Cuando cambies estructura de tablas, recrea la base para evitar columnas viejas en MySQL.
