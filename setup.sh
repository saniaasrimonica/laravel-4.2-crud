#!/usr/bin/env bash
set -euo pipefail
SOURCE="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
DEST="${1:-inventory-app}"
if [ -e "$DEST" ]; then echo 'Choose a new directory; destination already exists.' >&2; exit 1; fi
composer create-project laravel/laravel "$DEST" '^13.0' --no-interaction
cp -R "$SOURCE/overlay/." "$DEST/"
cd "$DEST"
composer require laravel/sanctum '^4.0' --no-interaction
composer require --dev laravel/pint --no-interaction
php artisan vendor:publish --provider='Laravel\Sanctum\SanctumServiceProvider' --no-interaction
php artisan migrate --force
php artisan test
echo "Ready. Run: cd $DEST && php artisan serve"
