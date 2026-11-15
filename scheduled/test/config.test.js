import { test } from 'node:test';
import assert from 'node:assert/strict';
import { generateKeyPairSync } from 'node:crypto';
import { loadConfig, ConfigError } from '../src/config.js';

const pem = generateKeyPairSync('ed25519').privateKey.export({ type: 'pkcs8', format: 'pem' });
const valid = {
  SCHEDULED_CENTRAL_ORIGIN: 'https://central.example.invalid',
  SCHEDULED_CENTRAL_AUDIENCE: 'titan-central',
  SCHEDULED_PRODUCT_ID: 'scheduled',
  SCHEDULED_LINK_KEY_ID: 'kid-test',
  SCHEDULED_LINK_PRIVATE_KEY_PEM: pem,
};

test('loads a complete configuration', () => {
  const config = loadConfig(valid);
  assert.equal(config.productId, 'scheduled');
  assert.equal(config.audience, 'titan-central');
  assert.equal(config.keyId, 'kid-test');
  assert.equal(config.centralOrigin, 'https://central.example.invalid');
});

test('reports every missing variable at once and no values', () => {
  assert.throws(() => loadConfig({}), (error) => {
    assert.ok(error instanceof ConfigError);
    for (const name of Object.keys(valid)) {
      assert.match(error.message, new RegExp(name));
    }
    return true;
  });
});

test('treats blank values as missing', () => {
  assert.throws(() => loadConfig({ ...valid, SCHEDULED_CENTRAL_AUDIENCE: '   ' }), /SCHEDULED_CENTRAL_AUDIENCE/);
});

test('rejects an invalid product id', () => {
  assert.throws(() => loadConfig({ ...valid, SCHEDULED_PRODUCT_ID: 'Scheduled' }), /SCHEDULED_PRODUCT_ID/);
});

test('rejects a non-https Central origin except localhost', () => {
  assert.throws(() => loadConfig({ ...valid, SCHEDULED_CENTRAL_ORIGIN: 'http://central.example.invalid' }), /SCHEDULED_CENTRAL_ORIGIN/);
  assert.throws(() => loadConfig({ ...valid, SCHEDULED_CENTRAL_ORIGIN: 'https://central.example.invalid/path' }), /SCHEDULED_CENTRAL_ORIGIN/);
  assert.doesNotThrow(() => loadConfig({ ...valid, SCHEDULED_CENTRAL_ORIGIN: 'http://localhost:4000' }));
});

test('rejects a non-Ed25519 or malformed key without echoing it', () => {
  const rsa = generateKeyPairSync('rsa', { modulusLength: 2048 }).privateKey.export({ type: 'pkcs8', format: 'pem' });
  assert.throws(() => loadConfig({ ...valid, SCHEDULED_LINK_PRIVATE_KEY_PEM: rsa }), /must be an Ed25519/);
  assert.throws(() => loadConfig({ ...valid, SCHEDULED_LINK_PRIVATE_KEY_PEM: 'not-a-key-secret' }), (error) => {
    assert.match(error.message, /not a valid PEM/);
    assert.doesNotMatch(error.message, /not-a-key-secret/);
    return true;
  });
});

test('accepts escaped newlines in the PEM variable', () => {
  const escaped = pem.replace(/\n/g, '\\n');
  assert.doesNotThrow(() => loadConfig({ ...valid, SCHEDULED_LINK_PRIVATE_KEY_PEM: escaped }));
});
