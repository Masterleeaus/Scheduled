import { test } from 'node:test';
import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import { generateKeyPairSync } from 'node:crypto';
import { fileURLToPath } from 'node:url';

const SCRIPT = fileURLToPath(new URL('../scripts/export-public-jwk.js', import.meta.url));

// Generated per run. No key material is committed.
const { privateKey, publicKey } = generateKeyPairSync('ed25519');
const pem = privateKey.export({ type: 'pkcs8', format: 'pem' });
const X = publicKey.export({ format: 'jwk' }).x;

const env = (extra) => ({
  PATH: process.env.PATH,
  NODE_ENV: 'test',
  SCHEDULED_CENTRAL_ORIGIN: 'https://central.example.invalid',
  SCHEDULED_CENTRAL_AUDIENCE: 'titan-central',
  SCHEDULED_PRODUCT_ID: 'scheduled',
  SCHEDULED_LINK_KEY_ID: 'test-kid-1',
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
  assert.equal(entry.kid, 'test-kid-1');
  assert.equal(entry.alg, 'EdDSA');
  assert.deepEqual(Object.keys(entry.jwk).sort(), ['crv', 'kty', 'x']);
  assert.equal(entry.jwk.x, X);
  assert.doesNotMatch(result.stdout, /"d"|PRIVATE/);
});

test('the export refuses to run without notBefore and names the variable only', () => {
  const result = spawnSync(process.execPath, [SCRIPT], { env: env({}), encoding: 'utf8' });
  assert.equal(result.status, 1);
  assert.match(result.stderr, /SCHEDULED_LINK_NOT_BEFORE/);
  assert.doesNotMatch(result.stderr, /PRIVATE KEY/);
  assert.equal(result.stdout, '');
});

test('the export refuses a test-vector kid outside the test environment', () => {
  const result = spawnSync(process.execPath, [SCRIPT], {
    env: env({
      NODE_ENV: 'production',
      SCHEDULED_LINK_KEY_ID: 'vector-kid-1',
      SCHEDULED_LINK_NOT_BEFORE: '2027-01-01T00:00:00.000Z',
    }),
    encoding: 'utf8',
  });
  assert.equal(result.status, 1);
  assert.match(result.stderr, /reserved for test vectors/);
  assert.equal(result.stdout, '');
});
