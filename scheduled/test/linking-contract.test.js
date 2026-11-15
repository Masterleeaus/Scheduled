import { test } from 'node:test';
import assert from 'node:assert/strict';
import { createHash, createPrivateKey, createPublicKey, sign as rawSign, verify } from 'node:crypto';
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

// Deterministic test vector. The seed is a public, fixed string hashed to 32 bytes.
// It is NOT a production key and must never be used to sign anything real.
const VECTOR_SEED = createHash('sha256').update('scheduled link-assertion test vector v1 (not a secret)').digest();
const VECTOR_PRIVATE_KEY = createPrivateKey({
  key: Buffer.concat([Buffer.from('302e020100300506032b657004220420', 'hex'), VECTOR_SEED]),
  format: 'der',
  type: 'pkcs8',
});
const VECTOR_KID = 'vector-kid-1';
const VECTOR_NOW = 1_800_000_000;
const VECTOR_NONCE = 'vector-nonce-0000000000001';
const VECTOR_X = 'Qmy_7imtT2zA3YCfi1E_wCSfw34ugcGzvmtcrOUrw9U';
const VECTOR_TOKEN = 'eyJhbGciOiJFZERTQSIsInR5cCI6InRpdGFuLWxpbmsrand0Iiwia2lkIjoidmVjdG9yLWtpZC0xIn0.eyJpc3MiOiJzY2hlZHVsZWQiLCJhdWQiOiJ0aXRhbi1jZW50cmFsIiwic3ViIjoiY2VudHJhbC1pZGVudGl0eS0xIiwib3JnIjoibG9ndG8tb3JnLWEiLCJsdSI6ImxvY2FsLXVzZXItMSIsImxhIjoibG9jYWwtYWNjb3VudC1hIiwibm9uY2UiOiJ2ZWN0b3Itbm9uY2UtMDAwMDAwMDAwMDAwMSIsImlhdCI6MTgwMDAwMDAwMCwiZXhwIjoxODAwMDAwMzAwfQ._OfNWQ_q7u4zh9L0AlIgV5cN98QRHSRS_EE0pvyugLv8Q-vag6Cc3FQZuHwxdvq1F2T7ovivEQJVdNudpxD3DA';

const base = (overrides = {}) => ({
  privateKey: VECTOR_PRIVATE_KEY,
  keyId: VECTOR_KID,
  issuer: 'scheduled',
  audience: 'titan-central',
  sub: 'central-identity-1',
  org: 'logto-org-a',
  lu: 'local-user-1',
  la: 'local-account-a',
  nonce: VECTOR_NONCE,
  nowSeconds: VECTOR_NOW,
  ...overrides,
});

const fakeStore = (nonce) => ({ consume: (value) => value === nonce });

const decode = (segment) => JSON.parse(Buffer.from(segment, 'base64url').toString('utf8'));

test('the vector key yields the expected public JWK', () => {
  assert.deepEqual({ ...publicJwk(VECTOR_PRIVATE_KEY) }, { kty: 'OKP', crv: 'Ed25519', x: VECTOR_X });
});

test('signLinkAssertion reproduces the fixed vector token byte for byte', () => {
  // Ed25519 is deterministic, so the same inputs must give the same token.
  assert.equal(signLinkAssertion(base()), VECTOR_TOKEN);
});

test('the vector token verifies with the public key alone, outside our verifier', () => {
  const [h, p, s] = VECTOR_TOKEN.split('.');
  const publicKey = createPublicKey({ key: { kty: 'OKP', crv: 'Ed25519', x: VECTOR_X }, format: 'jwk' });
  assert.equal(verify(null, Buffer.from(`${h}.${p}`), publicKey, Buffer.from(s, 'base64url')), true);
  assert.deepEqual(decode(h), { alg: 'EdDSA', typ: 'titan-link+jwt', kid: VECTOR_KID });
  assert.deepEqual(decode(p), {
    iss: 'scheduled',
    aud: 'titan-central',
    sub: 'central-identity-1',
    org: 'logto-org-a',
    lu: 'local-user-1',
    la: 'local-account-a',
    nonce: VECTOR_NONCE,
    iat: VECTOR_NOW,
    exp: VECTOR_NOW + 300,
  });
});

test('the vector token passes the verifier and returns the claims', () => {
  const payload = verifyLinkAssertion({
    token: VECTOR_TOKEN,
    publicKey: createPublicKey({ key: { kty: 'OKP', crv: 'Ed25519', x: VECTOR_X }, format: 'jwk' }),
    keyId: VECTOR_KID,
    issuer: 'scheduled',
    audience: 'titan-central',
    nonceStore: fakeStore(VECTOR_NONCE),
    nowSeconds: VECTOR_NOW,
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
  const key = { privateKey: VECTOR_PRIVATE_KEY, publicKey: createPublicKey(VECTOR_PRIVATE_KEY) };
  // Build a signed token directly so the signer's own check does not run.
  const b64 = (o) => Buffer.from(JSON.stringify(o)).toString('base64url');
  const input = `${b64({ alg: 'EdDSA', typ: 'titan-link+jwt', kid: VECTOR_KID })}.${b64({
    iss: 'scheduled', aud: 'titan-central', sub: 'jane@example.invalid', org: 'logto-org-a', lu: 'local-user-1',
    la: 'local-account-a', nonce: VECTOR_NONCE, iat: VECTOR_NOW, exp: VECTOR_NOW + 60,
  })}`;
  const token = `${input}.${rawSign(null, Buffer.from(input), key.privateKey).toString('base64url')}`;
  assert.throws(() => verifyLinkAssertion({
    token, publicKey: key.publicKey, keyId: VECTOR_KID, issuer: 'scheduled', audience: 'titan-central',
    nonceStore: fakeStore(VECTOR_NONCE), nowSeconds: VECTOR_NOW,
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
      token, publicKey: createPublicKey(VECTOR_PRIVATE_KEY), keyId: VECTOR_KID, issuer: 'scheduled',
      audience: 'titan-central', nonceStore: fakeStore(VECTOR_NONCE), nowSeconds: VECTOR_NOW,
    });
  };
  assert.doesNotThrow(at(VECTOR_NOW + 30));
  assert.throws(at(VECTOR_NOW + 31), /not yet valid/);
});

test('centralKeyEntry has the shape Central expects and requires notBefore', () => {
  const entry = centralKeyEntry({ privateKey: VECTOR_PRIVATE_KEY, keyId: VECTOR_KID, notBefore: '2027-01-01T00:00:00.000Z' });
  assert.deepEqual(Object.keys(entry).sort(), ['alg', 'jwk', 'kid', 'notBefore']);
  assert.deepEqual(entry.jwk, { kty: 'OKP', crv: 'Ed25519', x: VECTOR_X });
  assert.equal(entry.alg, 'EdDSA');
  assert.equal(entry.kid, VECTOR_KID);
  assert.throws(() => centralKeyEntry({ privateKey: VECTOR_PRIVATE_KEY, keyId: VECTOR_KID }), AssertionError);
  assert.throws(() => centralKeyEntry({ privateKey: VECTOR_PRIVATE_KEY, keyId: VECTOR_KID, notBefore: '2027-01-01' }), AssertionError);
  assert.throws(() => centralKeyEntry({
    privateKey: VECTOR_PRIVATE_KEY, keyId: VECTOR_KID, notBefore: '2027-01-01T00:00:00Z', notAfter: '2026-12-31T00:00:00Z',
  }), /later than notBefore/);
});

test('the identifier pattern is the one LINKING.md states', () => {
  assert.equal(IDENTIFIER_PATTERN.source, '^[A-Za-z0-9][A-Za-z0-9._:-]{7,127}$');
});
