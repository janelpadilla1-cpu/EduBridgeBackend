#!/usr/bin/env bash
set -euo pipefail

# Smoke test completo por PHPUnit.
# Cubre auth, CRUD, acciones de negocio y DELETE con payloads reales.
# Para pruebas HTTP externas usa Postman o scripts/smoke-test.ps1 en Windows.

if [ ! -f "artisan" ]; then
  echo "Ejecuta este script desde la raíz del proyecto EduBridgeBackend." >&2
  exit 1
fi

if [ ! -f "vendor/autoload.php" ]; then
  echo "No existe vendor/autoload.php. Ejecuta primero: composer install" >&2
  exit 1
fi

php artisan test --filter=SmokeApiTest
