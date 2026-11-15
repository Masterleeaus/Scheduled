#!/usr/bin/env python3
"""Run route bootstrap checks against extracted PlusAgency source (no database required)."""
import json
import pathlib
import re
import subprocess
import sys

root = pathlib.Path(__file__).resolve().parents[1]
app = root / 'core'
controllers = app / 'app/Http/Controllers'
checks = 0
for p in controllers.rglob('*.php'):
    source = p.read_text(errors='replace')
    match = re.search(r'public\s+function\s+__construct\s*\([^)]*\)\s*\{', source)
    if not match:
        continue
    depth = 1
    end = match.end()
    while depth and end < len(source):
        if source[end] == '{': depth += 1
        elif source[end] == '}': depth -= 1
        end += 1
    constructor = source[match.end():end-1]
    if re.search(r'(?:BS|BE|BasicExtra|PaymentGateway)::(?:first|where|find)', constructor):
        assert '$this->middleware(function ($request, $next)' in constructor, str(p)
        assert 'return $next($request);' in constructor, str(p)
        checks += 1
assert checks >= 30, f'Expected at least 30 DB-backed constructors deferred; got {checks}'
proc = subprocess.run(['php', 'artisan', 'route:list', '--json'], cwd=app,
                      text=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
assert proc.returncode == 0, proc.stderr[-3000:]
routes = json.loads(proc.stdout)
assert len(routes) >= 800, f'Only {len(routes)} routes; expected 800+'
assert any(r['uri'] == 'install/final/complete' and 'POST' in r['method'] for r in routes)
assert not any(r['uri'] == 'install/final/complete' and 'GET' in r['method'] for r in routes)
print(f'PASS: {checks} deferred constructors, {len(routes)} routes, POST-only installer finalisation')
