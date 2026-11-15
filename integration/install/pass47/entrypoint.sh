#!/bin/sh
set -eu
# The entire checkout is mounted read-only: execution happens in a disposable copy.
[ -f /source/core/artisan ] && [ -f /source/index.php ] || { echo 'Missing source distribution' >&2; exit 1; }
if [ ! -f /var/www/html/.pass47-copied ]; then
  cp -a /source/. /var/www/html/
  mkdir -p /var/www/html/core/storage /var/www/html/core/bootstrap/cache
  touch /var/www/html/.pass47-copied
fi
chown www-data:www-data /var/www/html/core
chown -R www-data:www-data /var/www/html/core/storage /var/www/html/core/bootstrap/cache
# The installer creates core/.env and key; never inject privileged DB credentials into the web image.
exec "$@"
