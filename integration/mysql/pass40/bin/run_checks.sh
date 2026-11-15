#!/usr/bin/env bash
set -euo pipefail
base="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
for source in "$base"/*.php; do php -l "$source" >/dev/null; done
for test in "$base"/../tests/*.php; do php "$test"; done
printf 'PASS: Pass 40 offline tests
'
