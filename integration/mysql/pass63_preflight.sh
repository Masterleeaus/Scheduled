#!/usr/bin/env bash
# Pass 63: run the real acceptance gates; a skipped MySQL test is never a pass.
set -uo pipefail
cd "$(dirname "$0")/../.."
php_bin="${PHP_BIN:-php}"
failed=0
printf '%s\n' 'PASS63: static and MariaDB acceptance checks'
if ! "$php_bin" -r 'exit(extension_loaded("pdo_mysql") ? 0 : 77);'; then
  printf '%s\n' 'BLOCKED: pdo_mysql missing; database acceptance cannot run.' >&2
  exit 77
fi
if [[ ! "${BOOKING_TEST_DATABASE:-}" =~ ^titan_booking_test_[A-Za-z0-9_]+$ ]]; then
  printf '%s\n' 'BLOCKED: BOOKING_TEST_DATABASE must designate an isolated titan_booking_test_* database.' >&2
  exit 78
fi
if [[ -z "${BOOKING_TEST_HOST:-}" || -z "${BOOKING_TEST_USER:-}" ]]; then
  printf '%s\n' 'BLOCKED: isolated BOOKING_TEST_HOST and BOOKING_TEST_USER are required.' >&2
  exit 78
fi
printf '%s\n' 'Running independent database schema, rollback, and lock probes...'
"$php_bin" integration/mysql/run_acceptance.php || exit $?
if [[ "${BOOKING_LIFECYCLE_ACK:-}" != 'I_UNDERSTAND_THIS_WRITES_TEST_DATA' || -z "${BOOKING_LIFECYCLE_FIXTURE:-}" ]]; then
  printf '%s\n' 'BLOCKED: fixture and explicit lifecycle acknowledgement required; no writes attempted.' >&2
  exit 78
fi
printf '%s\n' 'Running disposable real booking lifecycle...'
"$php_bin" integration/mysql/lifecycle/run_lifecycle.php || exit $?
printf '%s\n' 'Lifecycle executed. This alone does NOT prove simultaneous concurrency or production readiness.'
