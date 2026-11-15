#!/usr/bin/env bash
# READ-ONLY production environment assessment. Does not edit settings or enable booking writes.
set -u
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
fail=0
check(){ if "$@" >/dev/null 2>&1; then printf 'PASS %s\n' "$*"; else printf 'BLOCK %s\n' "$*"; fail=1; fi; }
check command -v php
check command -v composer
check command -v mysql
if command -v php >/dev/null 2>&1; then
 for ext in pdo_mysql pdo_sqlite curl mbstring openssl json dom xml fileinfo; do
   if php -r "exit(extension_loaded('$ext')?0:1);"; then echo "PASS PHP extension: $ext"; else echo "BLOCK PHP extension: $ext";fail=1;fi
 done
fi
for file in core/artisan core/composer.json core/.env core/vendor/autoload.php private/cleanto/objects/class_booking.php; do
 if [[ -f "$ROOT/$file" ]]; then echo "PASS $file"; else echo "BLOCK $file";fail=1;fi
done
if [[ -f "$ROOT/core/.env" ]]; then
 if grep -qE '^APP_DEBUG=true' "$ROOT/core/.env";then echo 'BLOCK APP_DEBUG=true';fail=1;fi
 if grep -qE '^APP_ENV=production' "$ROOT/core/.env";then echo 'PASS APP_ENV=production';else echo 'BLOCK APP_ENV not production';fail=1;fi
fi
printf 'This check does not run migrations, touch tenant data or enable payments.\n'
exit "$fail"
