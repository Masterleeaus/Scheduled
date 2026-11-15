#!/usr/bin/env bash
# Dedicated staging-only acceptance. Exit 77 means NO MariaDB outcome was tested.
set -euo pipefail
cd "$(dirname "$0")/../../.."
php_bin="${PHP_BIN:-php}"
"$php_bin" integration/mysql/pass64/preflight.php
if ! "$php_bin" -r 'exit(extension_loaded("pdo_mysql") ? 0 : 77);'; then
  printf '%s\n' 'SKIPPED: pdo_mysql absent; no database acceptance evidence exists.' >&2
  exit 77
fi
"$php_bin" integration/mysql/run_acceptance.php
BOOKING_LIFECYCLE_FIXTURE="$BOOKING_CONCURRENCY_FIXTURE" "$php_bin" integration/mysql/lifecycle/run_concurrency.php
"$php_bin" integration/mysql/lifecycle/run_lifecycle.php
printf '%s\n' 'PASS64: acceptance commands returned success; independently review their database evidence before release.'
