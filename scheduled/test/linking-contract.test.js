import { test } from 'node:test';
import assert from 'node:assert/strict';
import { createPublicKey, sign as rawSign, verify } from 'node:crypto';
import {
  signLinkAssertion,
  verifyLinkAssertion,
  publicJwk,
  centralKeyEntry,
  AssertionError,
  IDENTIFIER_PATTERN,
  NONCE_PATTERN,
  KID_PATTERN,
} from '../src/link-assertion.js';
import { makeKeys, ISSUER, AUDIENCE, KEY_ID } from './helpers.js';

// Keys are generated per run by makeKeys(). No test key is derived from a public value.
const keys = makeKeys();
const NOW = 1_800_000_000;
const NONCE = 'contract-nonce-000000000001';
const X = keys.publicKey.export({ format: 'jwk' }).x;

const base = (overrides = {}) => ({
  privateKey: keys.privateKey,
  keyId: KEY_ID,
  issuer: ISSUER,
  audience: AUDIENCE,
  sub: 'central-identity-1',
  org: 'logto-org-a',
  lu: 'local-user-1',
  la: 'local-account-a',
  nonce: NONCE,
  nowSeconds: NOW,
  ...overrides,
});

const fakeStore = (nonce) => ({ consume: (value) => value === nonce });

const decode = (segment) => JSON.parse(Buffer.from(segment, 'base64url').toString('utf8'));

test('publicJwk returns only the public Ed25519 members', () => {
  assert.deepEqual({ ...publicJwk(keys.privateKey) }, { kty: 'OKP', crv: 'Ed25519', x: X });
});

test('signing is deterministic: the same inputs give the same token', () => {
  // Ed25519 is deterministic, so the same key and inputs must give the same token.
  assert.equal(signLinkAssertion(base()), signLinkAssertion(base()));
});

test('a signed token verifies with the public key alone, outside our verifier', () => {
  const token = signLinkAssertion(base());
  const [h, p, s] = token.split('.');
  assert.equal(verify(null, Buffer.from(`${h}.${p}`), keys.publicKey, Buffer.from(s, 'base64url')), true);
  assert.deepEqual(decode(h), { alg: 'EdDSA', typ: 'titan-link+jwt', kid: KEY_ID });
  assert.deepEqual(decode(p), {
    iss: ISSUER,
    aud: AUDIENCE,
    sub: 'central-identity-1',
    org: 'logto-org-a',
    lu: 'local-user-1',
    la: 'local-account-a',
    nonce: NONCE,
    iat: NOW,
    exp: NOW + 300,
  });
});

test('a signed token passes the verifier and returns the claims', () => {
  const payload = verifyLinkAssertion({
    token: signLinkAssertion(base()),
    publicKey: keys.publicKey,
    keyId: KEY_ID,
    issuer: ISSUER,
    audience: AUDIENCE,
    nonceStore: fakeStore(NONCE),
    nowSeconds: NOW,
  });
  assert.equal(payload.exp - payload.iat, 300);
});

test('identifiers must match the LINKING.md pattern: emails and display names are refused', () => {
  for (const bad of ['jane@example.invalid', 'Jane Smith', 'short', '.leadingdot1', 'x'.repeat(129)]) {
    assert.throws(() => signLinkAssertion(base({ sub: bad })), AssertionError, bad);
  }
  assert.doesNotThrow(() => signLinkAssertion(base({ sub: 'abcdefgh' })));
  assert.doesNotThrow(() => signLinkAssertion(base({ sub: 'a'.repeat(128) })));
});

test('the verifier refuses an email-shaped identifier even with a valid signature', () => {
  // Build a signed token directly so the signer's own check does not run.
  const b64 = (o) => Buffer.from(JSON.stringify(o)).toString('base64url');
  const input = `${b64({ alg: 'EdDSA', typ: 'titan-link+jwt', kid: KEY_ID })}.${b64({
    iss: ISSUER, aud: AUDIENCE, sub: 'jane@example.invalid', org: 'logto-org-a', lu: 'local-user-1',
    la: 'local-account-a', nonce: NONCE, iat: NOW, exp: NOW + 60,
  })}`;
  const token = `${input}.${rawSign(null, Buffer.from(input), keys.privateKey).toString('base64url')}`;
  assert.throws(() => verifyLinkAssertion({
    token, publicKey: keys.publicKey, keyId: KEY_ID, issuer: ISSUER, audience: AUDIENCE,
    nonceStore: fakeStore(NONCE), nowSeconds: NOW,
  }), /opaque identifier/);
});

test('nonces must be 22 to 128 base64url characters', () => {
  assert.equal(NONCE_PATTERN.test('a'.repeat(21)), false);
  assert.equal(NONCE_PATTERN.test('a'.repeat(22)), true);
  assert.equal(NONCE_PATTERN.test('a'.repeat(128)), true);
  assert.equal(NONCE_PATTERN.test('a'.repeat(129)), false);
  assert.equal(NONCE_PATTERN.test('abc+def/ghi-jkl_mno-pqr1'), false);
  for (const bad of ['a'.repeat(21), 'a'.repeat(129), 'abc+def/ghi-jkl_mno-pqr1']) {
    assert.throws(() => signLinkAssertion(base({ nonce: bad })), AssertionError, bad);
  }
});

test('kid must be 1 to 64 letters, digits, dots, underscores or hyphens', () => {
  assert.equal(KID_PATTERN.test(''), false);
  assert.equal(KID_PATTERN.test('bad kid'), false);
  assert.equal(KID_PATTERN.test('a'.repeat(64)), true);
  assert.equal(KID_PATTERN.test('a'.repeat(65)), false);
  assert.throws(() => signLinkAssertion(base({ keyId: 'bad kid' })), AssertionError);
});

test('lifetime must be an integer from 1 to 300 seconds', () => {
  for (const bad of [0, 301, 1.5]) {
    assert.throws(() => signLinkAssertion(base({ lifetimeSeconds: bad })), AssertionError, String(bad));
  }
  assert.doesNotThrow(() => signLinkAssertion(base({ lifetimeSeconds: 300 })));
});

test('iat may lead the verifier clock by at most 30 seconds', () => {
  const at = (iat) => {
    const token = signLinkAssertion(base({ nowSeconds: iat }));
    return () => verifyLinkAssertion({
      token, publicKey: keys.publicKey, keyId: KEY_ID, issuer: ISSUER,
      audience: AUDIENCE, nonceStore: fakeStore(NONCE), nowSeconds: NOW,
    });
  };
  assert.doesNotThrow(at(NOW + 30));
  assert.throws(at(NOW + 31), /not yet valid/);
});

test('centralKeyEntry has the shape Central expects and requires notBefore', () => {
  const entry = centralKeyEntry({ privateKey: keys.privateKey, keyId: KEY_ID, notBefore: '2027-01-01T00:00:00.000Z' });
  assert.deepEqual(Object.keys(entry).sort(), ['alg', 'jwk', 'kid', 'notBefore']);
  assert.deepEqual(entry.jwk, { kty: 'OKP', crv: 'Ed25519', x: X });
  assert.equal(entry.alg, 'EdDSA');
  assert.equal(entry.kid, KEY_ID);
  assert.throws(() => centralKeyEntry({ privateKey: keys.privateKey, keyId: KEY_ID }), AssertionError);
  assert.throws(() => centralKeyEntry({ privateKey: keys.privateKey, keyId: KEY_ID, notBefore: '2027-01-01' }), AssertionError);
  assert.throws(() => centralKeyEntry({
    privateKey: keys.privateKey, keyId: KEY_ID, notBefore: '2027-01-01T00:00:00Z', notAfter: '2026-12-31T00:00:00Z',
  }), /later than notBefore/);
});

test('test-vector kids are refused outside the test environment', () => {
  const previous = process.env.NODE_ENV;
  process.env.NODE_ENV = 'production';
  try {
    assert.throws(() => signLinkAssertion(base({ keyId: 'vector-kid-1' })), /reserved for test vectors/);
    assert.throws(() => signLinkAssertion(base({ keyId: 'test-kid-1' })), /reserved for test vectors/);
    assert.throws(() => centralKeyEntry({
      privateKey: keys.privateKey, keyId: 'vector-kid-1', notBefore: '2027-01-01T00:00:00.000Z',
    }), /reserved for test vectors/);
    assert.throws(() => verifyLinkAssertion({
      token: 'a.b.c', publicKey: keys.publicKey, keyId: 'vector-kid-1', issuer: ISSUER,
      audience: AUDIENCE, nonceStore: fakeStore(NONCE), nowSeconds: NOW,
    }), /reserved for test vectors/);
    // Production kids are unaffected.
    assert.doesNotThrow(() => signLinkAssertion(base({ keyId: 'scheduled-link-2026' })));
  } finally {
    if (previous === undefined) delete process.env.NODE_ENV;
    else process.env.NODE_ENV = previous;
  }
});

test('the identifier pattern is the one LINKING.md states', () => {
  assert.equal(IDENTIFIER_PATTERN.source, '^[A-Za-z0-9][A-Za-z0-9._:-]{7,127}$');
});
