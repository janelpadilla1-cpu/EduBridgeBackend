# Seeders

Carga inicial mínima para desarrollo local.

## Seeder principal

```txt
database/seeders/DatabaseSeeder.php
```

## Datos iniciales

`RoleUsuarioSeeder` crea roles base como:

- `ESTUDIANTE`
- `AUXILIAR`
- `COORDINADOR`
- `ADMINISTRADOR`

## Ejecutar

```bash
php artisan db:seed
```

O reiniciar todo:

```bash
php artisan migrate:fresh --seed
```
