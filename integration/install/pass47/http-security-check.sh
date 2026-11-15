#!/usr/bin/env bash
set -euo pipefail
base="http://127.0.0.1:${PA47_HTTP_PORT:-18080}"
# Must test local-only HTTP; an optional server redirect must not be followed
# because success on a remote address would not test this host's deny rules.
command -v curl >/dev/null || { echo 'curl required' >&2; exit 77; }
failed=0
for path in '/private/cleanto/vendor/composer/installed.json' '/private/cleanto/assets/stripe/composer.json' '/development/README.md' '/scripts/README.md' '/PASS05_EVIDENCE/example.txt' '/PASS48_REPORT.md' '/PASS48_MANIFEST.json' '/RECOVERY_STATUS_PASS62.json' '/core/composer.json' '/core/.env.example' '/installer/database.sql' '/tools/install-preflight.php' '/tests/pass46-feature-schema-lock.php' '/integration/install/pass47/compose.yaml' '/docs/PASS47_INSTALL_ACCEPTANCE.md' '/PASS47_MANIFEST.json' '/core/vendor/autoload.php' '/core/storage/logs/laravel.log'; do
  result="$(curl -sS --max-time 15 -o /dev/null -w '%{http_code}' --path-as-is "$base$path")" || { echo "FAIL: HTTP unavailable for $path" >&2; exit 1; }
  case "$result" in
    403|404) echo "PASS: $path denied ($result)" ;;
    *) echo "FAIL: private $path returned HTTP $result" >&2; failed=1 ;;
  esac
done
# The public entrypoint should respond; do not falsely equate installer HTTP response with full installation.
public="$(curl -sS --max-time 15 -o /dev/null -w '%{http_code}' "$base/")" || public=000
case "$public" in
  000|500|502|503|504) echo "WARN: public entrypoint HTTP $public (investigate before installation)" >&2; failed=1 ;;
  *) echo "INFO: public entrypoint HTTP $public; application installation still requires manual verification" ;;
esac
exit "$failed"
