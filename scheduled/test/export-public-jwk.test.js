import { test } from 'node:test';
import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import { createHash, createPrivateKey } from 'node:crypto';
import { fileURLToPath } from 'node:url';

const SCRIPT = fileURLToPath(new URL('../scripts/export-public-jwk.js', import.meta.url));

// Fixed public test seed, same as linking-contract.test.js. Not a production key.
const seed = createHash('sha256').update('scheduled link-assertion test vector v1 (not a secret)').digest();
const pem = createPrivateKey({
  key: Buffer.concat([Buffer.from('302e020100300506032b657004220420', 'hex'), seed]),
  format: 'der',
  type: 'pkcs8',
}).export({ type: 'pkcs8', format: 'pem' });

const env = (extra) => ({
  PATH: process.env.PATH,
  SCHEDULED_CENTRAL_ORIGIN: 'https://central.example.invalid',
  SCHEDULED_CENTRAL_AUDIENCE: 'titan-central',
  SCHEDULED_PRODUCT_ID: 'scheduled',
  SCHEDULED_LINK_KEY_ID: 'vector-kid-1',
  SCHEDULED_LINK_PRIVATE_KEY_PEM: pem,
  ...extra,
});

test('the export prints a Central key entry with public material only', () => {
  const result = spawnSync(process.execPath, [SCRIPT], {
    env: env({ SCHEDULED_LINK_NOT_BEFORE: '2027-01-01T00:00:00.000Z' }),
    encoding: 'utf8',
  });
  assert.equal(result.status, 0, result.stderr);
  const entry = JSON.parse(result.stdout);
  assert.deepEqual(Object.keys(entry).sort(), ['alg', 'jwk', 'kid', 'notBefore']);
  assert.equal(entry.kid, 'vector-kid-1');
  assert.equal(entry.alg, 'EdDSA');
  assert.deepEqual(Object.keys(entry.jwk).sort(), ['crv', 'kty', 'x']);
  assert.equal(entry.jwk.x, 'Qmy_7imtT2zA3YCfi1E_wCSfw34ugcGzvmtcrOUrw9U');
  assert.doesNotMatch(result.stdout, /"d"|PRIVATE/);
});

test('the export refuses to run without notBefore and names the variable only', () => {
  const result = spawnSync(process.execPath, [SCRIPT], { env: env({}), encoding: 'utf8' });
  assert.equal(result.status, 1);
  assert.match(result.stderr, /SCHEDULED_LINK_NOT_BEFORE/);
  assert.doesNotMatch(result.stderr, /PRIVATE KEY/);
  assert.equal(result.stdout, '');
});
