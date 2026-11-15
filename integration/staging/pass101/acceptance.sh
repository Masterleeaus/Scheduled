#!/usr/bin/env bash
# Run inside the disposable docker network. Never auto-seed credentials or fake bookings.
set -euo pipefail
cd /workspace
php -r 'if (!extension_loaded("pdo_mysql")) {fwrite(STDERR,"Missing pdo_mysql\n"); exit(77);}'
for file in /fixtures/lifecycle.json /fixtures/concurrency.json; do
  test -f "$file" || { echo "BLOCKED: missing $file" >&2; exit 78; }
done
# Verify real imported table engines and synthetic relationships before booking tests.
php integration/staging/pass113/live_probe.php
# Both fixtures must target the exact disposable database and separate future windows.
php integration/mysql/pass64/preflight.php
# A real Cleanto schema and fixture rows must have been provisioned separately.
# This first check fails rather than inventing tables or records.
# Capture independent executable program outputs, then require consistent structured evidence.
# No missing/empty/malformed report can silently pass the staging gate.
evidence_dir="$(mktemp -d)"
chmod 700 "$evidence_dir"
trap 'rm -rf "$evidence_dir"' EXIT
php integration/mysql/run_acceptance.php > "$evidence_dir/infrastructure.json"
BOOKING_LIFECYCLE_FIXTURE=/fixtures/concurrency.json php integration/mysql/lifecycle/run_concurrency.php > "$evidence_dir/concurrency.json"
BOOKING_LIFECYCLE_FIXTURE=/fixtures/lifecycle.json php integration/mysql/lifecycle/run_lifecycle.php > "$evidence_dir/lifecycle.json"
php integration/staging/pass101/verify_reports.php "$evidence_dir/infrastructure.json" "$evidence_dir/lifecycle.json" "$evidence_dir/concurrency.json"
# Raw reports remain visible on stdout for the staging operator's own independent audit.
for report in infrastructure lifecycle concurrency; do
  printf '\n=== DISPOSABLE DB EVIDENCE: %s ===\n' "$report"
  cat "$evidence_dir/$report.json"
done
printf '\nPASS102: disposable evidence gate completed; NOT a production release authorization.\n'
