import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const root = new URL('../', import.meta.url);
const read = (relative) => readFileSync(new URL(relative, root), 'utf8');

// Names the code reads: every SCHEDULED_* token in src/config.js.
const codeNames = [...new Set(read('src/config.js').match(/\bSCHEDULED_[A-Z0-9_]+\b/g) ?? [])].sort();

// Names the template declares as active assignments (commented lines are ignored).
const templateNames = new Set([...read('.env.example').matchAll(/^([A-Z][A-Z0-9_]*)=/gm)].map((m) => m[1]));

test('config.js reads a non-empty set of SCHEDULED_* variables', () => {
  assert.ok(codeNames.length >= 5, `expected at least 5 names, found ${codeNames.length}`);
});

test('every SCHEDULED_* variable read by src/config.js is declared in .env.example', () => {
  const missing = codeNames.filter((name) => !templateNames.has(name));
  assert.deepEqual(missing, [], `missing from .env.example: ${missing.join(', ')}`);
});
