#!/usr/bin/env bash
# Disposable database from independently reviewed Cleanto DDL and synthetic fixture SQL.
set -euo pipefail
cd "$(dirname "$0")"
# Compose names default to the working directory; sharing that project makes
# cleanup of one test destroy the other test's database. Allocate a per-run ID.
if [[ -n "${PA101_COMPOSE_PROJECT:-}" ]]; then
  [[ "$PA101_COMPOSE_PROJECT" =~ ^pa101_[a-z0-9]{10,40}$ ]] || { echo 'BLOCKED: unsafe Compose project override' >&2; exit 78; }
  project="$PA101_COMPOSE_PROJECT"
else
  command -v od >/dev/null || { echo 'BLOCKED: od required for independent run identity' >&2; exit 77; }
  suffix="$(od -An -N12 -tx1 /dev/urandom | tr -d ' \n')"
  [[ "$suffix" =~ ^[a-f0-9]{24}$ ]] || { echo 'BLOCKED: unable to generate unique run identity' >&2; exit 78; }
  project="pa101_${suffix}"
fi
# Every Compose command below must use the same explicit project.
compose() { docker compose -p "$project" -f compose.yaml "$@"; }
command -v docker >/dev/null || { echo 'BLOCKED: Docker Compose required on staging host' >&2; exit 77; }
for name in PA101_FIXTURES_DIR PA101_SCHEMA_SQL PA101_SEED_SQL PA101_DB_PASSWORD PA101_ROOT_PASSWORD; do
  [[ -n "${!name:-}" ]] || { echo "BLOCKED: $name is required" >&2; exit 78; }
done
for name in PA101_FIXTURES_DIR PA101_SCHEMA_SQL PA101_SEED_SQL; do
  [[ "${!name}" = /* ]] || { echo "BLOCKED: $name must be an absolute path" >&2; exit 78; }
done
[[ -d "$PA101_FIXTURES_DIR" && -f "$PA101_FIXTURES_DIR/lifecycle.json" && -f "$PA101_FIXTURES_DIR/concurrency.json" ]] || { echo 'BLOCKED: separately reviewed lifecycle and concurrency JSON fixtures required' >&2; exit 78; }
[[ -f "$PA101_SCHEMA_SQL" && -s "$PA101_SCHEMA_SQL" && -f "$PA101_SEED_SQL" && -s "$PA101_SEED_SQL" ]] || { echo 'BLOCKED: authentic reviewed Cleanto schema and synthetic test rows SQL required' >&2; exit 78; }
[[ "$PA101_DB_PASSWORD" != "$PA101_ROOT_PASSWORD" ]] || { echo 'BLOCKED: database passwords must differ' >&2; exit 78; }
[[ "${PA101_DISPOSABLE_ACK:-}" == 'YES_DESTROY_TEST_DATABASE' ]] || { echo 'BLOCKED: set PA101_DISPOSABLE_ACK=YES_DESTROY_TEST_DATABASE' >&2; exit 78; }
# Always dismantle the disposable database, including on SQL import/test failure.
cleanup() { compose down --volumes --remove-orphans >/dev/null 2>&1 || true; }
trap cleanup EXIT

# Validate schema/seed/fixture content before creating any database containers.
command -v python3 >/dev/null || { echo 'BLOCKED: python3 required for input validation' >&2; exit 77; }
preflight=(python3 ../pass110/validate_inputs.py --schema "$PA101_SCHEMA_SQL" --seed "$PA101_SEED_SQL" --fixtures "$PA101_FIXTURES_DIR")
if [[ -n "${PA101_REVIEW_MANIFEST:-}" ]]; then preflight+=(--manifest "$PA101_REVIEW_MANIFEST"); fi
"${preflight[@]}"
compose config --quiet
compose up -d --build --wait mariadb-test
# SQL is streamed directly via stdin; no database port is published and no secrets in command arguments.
for sql in "$PA101_SCHEMA_SQL" "$PA101_SEED_SQL"; do
  compose exec -T mariadb-test sh -c 'exec mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" titan_booking_test_acceptance' < "$sql"
done
# Test failures propagate while EXIT trap still erases the test database.
compose run --rm booking-test
echo 'Pass 101 acceptance commands exited successfully; independently inspect booking and order readback evidence.'
