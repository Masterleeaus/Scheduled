#!/usr/bin/env bash
set -euo pipefail
here="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
cd "$here"
[[ "${PA47_DISPOSABLE_ACK:-}" == 'YES_DESTROY_DISPOSABLE_DB' ]] || { echo 'Set PA47_DISPOSABLE_ACK=YES_DESTROY_DISPOSABLE_DB' >&2; exit 78; }
for name in PA47_DB_PASSWORD PA47_ROOT_PASSWORD; do
  secret="${!name:-}"
  [[ -n "$secret" && "${#secret}" -ge 16 ]] || { echo "Set $name to a unique secret of at least 16 characters" >&2; exit 78; }
done
[[ "$PA47_DB_PASSWORD" != "$PA47_ROOT_PASSWORD" ]] || { echo 'Database root and installer secrets must differ' >&2; exit 78; }
command -v docker >/dev/null || { echo 'Docker Compose required; no live test run on this host' >&2; exit 77; }
docker compose version >/dev/null 2>&1 || { echo 'Docker Compose v2 is required' >&2; exit 77; }
[[ "${PA47_HTTP_PORT:-18080}" =~ ^[0-9]{1,5}$ ]] && (( 10#${PA47_HTTP_PORT:-18080} >= 1024 && 10#${PA47_HTTP_PORT:-18080} <= 65535 )) || { echo 'PA47_HTTP_PORT must be an unprivileged numeric port (1024–65535)' >&2; exit 78; }
if [[ "${PA47_NONINTERACTIVE:-0}" == 1 && -z "${PA47_EVIDENCE_DIR:-}" ]]; then
  echo 'Noninteractive mode requires an explicit PA47_EVIDENCE_DIR to retain results' >&2; exit 78
fi
if [[ -n "${PA47_EVIDENCE_DIR:-}" ]]; then
  mkdir -p -- "$PA47_EVIDENCE_DIR"
  evidence="$(cd -- "$PA47_EVIDENCE_DIR" && pwd -P)"
  [[ "$evidence" != / && "$evidence" != "$here" && "$evidence" != "$here/"* ]] || { echo 'Evidence directory must be separate from test source' >&2; exit 78; }
else evidence=''; fi
project="pa47_$(od -An -N8 -tx1 /dev/urandom | tr -d ' \n')"
compose() { docker compose -p "$project" -f compose.yaml "$@"; }
cleanup() { compose down --volumes --remove-orphans >/dev/null 2>&1 || true; }
trap cleanup EXIT
compose config --quiet
compose up --build --wait -d
compose exec -T app php -r 'exit(extension_loaded("pdo_mysql") && extension_loaded("mysqli") ? 0 : 1);'
if [[ -n "$evidence" ]]; then
  compose exec -T app php /var/www/html/tools/install-preflight.php --json > "$evidence/preflight.json"
  ./http-security-check.sh | tee "$evidence/http-security.txt"
else
  compose exec -T app php /var/www/html/tools/install-preflight.php --json
  ./http-security-check.sh
fi
if [[ "${PA47_NONINTERACTIVE:-0}" == 1 ]]; then
  echo 'Pre-install checks passed; no administrator, template, payment or booking transaction was tested.'
  exit 0
fi
cat <<EOFMSG
STAGING HTTP READY at http://127.0.0.1:${PA47_HTTP_PORT:-18080}
Installer DB host: db / database: pa47_fresh_install / user: pa47_installer
Run the first-install wizard manually, then verify admin login, 29 templates and Cleanto bookings.
Press Enter to destroy BOTH the disposable application volume and database.
EOFMSG
if [[ ! -r /dev/tty ]]; then echo 'Interactive console required; use PA47_NONINTERACTIVE=1 with PA47_EVIDENCE_DIR for pre-install probes' >&2; exit 78; fi
read -r -p 'Press Enter to tear down this disposable environment: ' _ </dev/tty
