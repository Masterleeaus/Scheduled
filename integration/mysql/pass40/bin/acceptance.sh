#!/usr/bin/env bash
set -euo pipefail
here="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
root="$(cd "$here/../../../.." && pwd)"
compose="$root/integration/mysql/pass40/docker/compose.yaml"
if [[ "${1:-}" != "preflight" && "${1:-}" != "lifecycle" && "${1:-}" != "concurrency" ]]; then echo 'Usage: acceptance.sh preflight|lifecycle|concurrency' >&2; exit 64; fi
if ! command -v docker >/dev/null; then echo 'SKIP: Docker not available' >&2; exit 77; fi
if [[ -z "${BOOKING_TEST_PASSWORD:-}" || -z "${BOOKING_TEST_ROOT_PASSWORD:-}" ]]; then echo 'FAIL: disposable database passwords required' >&2; exit 2; fi
case "$1" in
 preflight) target=integration/mysql/run_acceptance.php;;
 lifecycle) target=integration/mysql/lifecycle/run_lifecycle.php;;
 concurrency) target=integration/mysql/lifecycle/run_concurrency.php;;
esac
if [[ "$1" != preflight ]]; then
 [[ "${BOOKING_LIFECYCLE_ACK:-}" == 'I_UNDERSTAND_THIS_WRITES_TEST_DATA' ]] || { echo 'FAIL: explicit test acknowledgement required' >&2; exit 2; }
 [[ "${BOOKING_ACCEPT_TEST_ONLY:-}" == 'YES_DISPOSABLE_ONLY' ]] || { echo 'FAIL: test override required' >&2; exit 2; }
 [[ -n "${BOOKING_LIFECYCLE_FIXTURE:-}" ]] || { echo 'FAIL: fixture path required' >&2; exit 2; }
 [[ "${BOOKING_LIFECYCLE_FIXTURE}" == /workspace/* ]] || { echo 'FAIL: fixture must be mounted under /workspace' >&2; exit 2; }
 [[ "${BOOKING_LIFECYCLE_FIXTURE}" != *'..'* ]] || { echo 'FAIL: fixture traversal is forbidden' >&2; exit 2; }
fi
cd "$root"
docker compose -f "$compose" run --rm php "$target"
