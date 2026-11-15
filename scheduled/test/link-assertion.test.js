import { test } from 'node:test';
import assert from 'node:assert/strict';
import { randomBytes, sign as edSign } from 'node:crypto';
import {
  signLinkAssertion,
  verifyLinkAssertion,
  publicJwk,
  isVerifiedAssertion,
  AssertionError,
  FORBIDDEN_CLAIMS,
} from '../src/link-assertion.js';
import { NonceStore } from '../src/nonce-store.js';
import { makeKeys, baseClaims, ISSUER, AUDIENCE, KEY_ID } from './helpers.js';

const now = 1_800_000_000;

function sign(keys, overrides = {}) {
  const { issuer, audience, sub, org, lu, la, nonce } = baseClaims({ nowSeconds: now, ...overrides });
  return signLinkAssertion({
    privateKey: keys.privateKey, keyId: KEY_ID, issuer, audience, sub, org, lu, la, nonce, nowSeconds: now,
  });
}

function verify(keys, token, store) {
  return verifyLinkAssertion({
    token, publicKey: keys.publicKey, keyId: KEY_ID, issuer: ISSUER, audience: AUDIENCE, nonceStore: store, nowSeconds: now,
  });
}

// Build a token by hand, bypassing the signer, to exercise verifier rejections.
function forge(keys, { header = {}, payload = {} } = {}) {
  const b64 = (o) => Buffer.from(JSON.stringify(o)).toString('base64url');
  const h = { alg: 'EdDSA', typ: 'titan-link+jwt', kid: KEY_ID, ...header };
  const p = {
    iss: ISSUER, aud: AUDIENCE, sub: 'central-identity-2', org: 'logto-org-b', lu: 'local-user-2', la: 'local-account-b', nonce: 'test-nonce-0000000000000002',
    iat: now, exp: now + 300, ...payload,
  };
  const input = `${b64(h)}.${b64(p)}`;
  return `${input}.${edSign(null, Buffer.from(input), keys.privateKey).toString('base64url')}`;
}

const newStore = () => new NonceStore({ now: () => now });

test('signature round-trip verifies and returns the claims', () => {
  const keys = makeKeys();
  const store = newStore();
  const token = sign(keys, { nonce: store.issue() });
  const payload = verify(keys, token, store);
  assert.equal(payload.iss, ISSUER);
  assert.equal(payload.aud, AUDIENCE);
  assert.equal(payload.sub, 'central-identity-1');
  assert.equal(payload.org, 'logto-org-a');
  assert.equal(payload.iat, now);
  assert.equal(payload.exp, now + 300);
  assert.ok(isVerifiedAssertion(payload));
});

test('header carries EdDSA, titan-link+jwt and the kid', () => {
  const keys = makeKeys();
  const header = JSON.parse(Buffer.from(sign(keys).split('.')[0], 'base64url').toString());
  assert.deepEqual(header, { alg: 'EdDSA', typ: 'titan-link+jwt', kid: KEY_ID });
});

test('public JWK holds only kty, crv and x, the members Central accepts', () => {
  const keys = makeKeys();
  const jwk = publicJwk(keys.privateKey);
  assert.deepEqual(Object.keys(jwk).sort(), ['crv', 'kty', 'x']);
  assert.equal(jwk.kty, 'OKP');
  assert.equal(jwk.crv, 'Ed25519');
  assert.doesNotMatch(JSON.stringify(jwk), /"d"/);
});

test('a token signed by another key is rejected', () => {
  const store = newStore();
  const token = sign(makeKeys(), { nonce: store.issue() });
  assert.throws(() => verify(makeKeys(), token, store), /signature verification failed/);
});

test('a tampered payload fails the signature check', () => {
  const keys = makeKeys();
  const store = newStore();
  const [h, , s] = sign(keys, { nonce: store.issue() }).split('.');
  const evil = Buffer.from(JSON.stringify({
    iss: ISSUER, aud: AUDIENCE, sub: 'central-identity-x', org: 'logto-org-other', lu: 'local-user-x', la: 'local-account-x', nonce: 'test-nonce-0000000000000003', iat: now, exp: now + 1,
  })).toString('base64url');
  assert.throws(() => verify(keys, `${h}.${evil}.${s}`, store), /signature verification failed/);
});

test('alg none, wrong typ, wrong kid and extra header fields are rejected', () => {
  const keys = makeKeys();
  const store = newStore();
  const noneHeader = Buffer.from(JSON.stringify({ alg: 'none', typ: 'titan-link+jwt', kid: KEY_ID })).toString('base64url');
  const body = sign(keys, { nonce: store.issue() }).split('.')[1];
  assert.throws(() => verify(keys, `${noneHeader}.${body}.`, store), /alg must be EdDSA/);
  assert.throws(() => verify(keys, forge(keys, { header: { typ: 'JWT' } }), store), /typ must be/);
  assert.throws(() => verify(keys, forge(keys, { header: { kid: 'other-kid' } }), store), /kid does not match/);
  assert.throws(() => verify(keys, forge(keys, { header: { extra: 1 } }), store), /exactly alg, kid and typ/);
});

test('forbidden profile claims are rejected by the verifier', () => {
  const keys = makeKeys();
  for (const claim of FORBIDDEN_CLAIMS) {
    const store = newStore();
    const token = forge(keys, { payload: { nonce: store.issue(), [claim]: 'x' } });
    assert.throws(() => verify(keys, token, store), new RegExp(`forbidden claim present: ${claim}`), claim);
  }
});

test('the signer emits only whitelisted claims, so forbidden claims cannot be added', () => {
  const keys = makeKeys();
  const store = newStore();
  const token = sign(keys, { nonce: store.issue(), email: 'a@example.invalid', name: 'Someone' });
  const payload = JSON.parse(Buffer.from(token.split('.')[1], 'base64url').toString());
  for (const claim of FORBIDDEN_CLAIMS) {
    assert.equal(Object.hasOwn(payload, claim), false, claim);
  }
});

test('the signer refuses a lifetime above 300 seconds', () => {
  const keys = makeKeys();
  const { issuer, audience, sub, org, lu, la, nonce } = baseClaims({ nowSeconds: now });
  assert.throws(
    () => signLinkAssertion({ privateKey: keys.privateKey, keyId: KEY_ID, issuer, audience, sub, org, lu, la, nonce, nowSeconds: now, lifetimeSeconds: 301 }),
    AssertionError,
  );
});

test('a lifetime above 300 seconds is rejected by the verifier', () => {
  const keys = makeKeys();
  const store = newStore();
  const token = forge(keys, { payload: { nonce: store.issue(), exp: now + 301 } });
  assert.throws(() => verify(keys, token, store), /at most 300 seconds/);
});

test('expired and not-yet-valid assertions are rejected', () => {
  const keys = makeKeys();
  const store = newStore();
  const expired = forge(keys, { payload: { nonce: store.issue(), iat: now - 200, exp: now - 1 } });
  assert.throws(() => verify(keys, expired, store), /expired/);
  const future = forge(keys, { payload: { nonce: store.issue(), iat: now + 60, exp: now + 300 } });
  assert.throws(() => verify(keys, future, store), /not yet valid/);
});

test('a wrong issuer or audience is rejected', () => {
  const keys = makeKeys();
  const store = newStore();
  assert.throws(() => verify(keys, forge(keys, { payload: { nonce: store.issue(), iss: 'sorted' } }), store), /iss does not match/);
  assert.throws(() => verify(keys, forge(keys, { payload: { nonce: store.issue(), aud: 'other' } }), store), /aud does not match/);
});

test('nonces are single use', () => {
  const keys = makeKeys();
  const store = newStore();
  const token = sign(keys, { nonce: store.issue() });
  verify(keys, token, store);
  assert.throws(() => verify(keys, token, store), /nonce is unknown, already used or expired/);
});

test('an unissued nonce is rejected', () => {
  const keys = makeKeys();
  const unissued = randomBytes(16).toString('base64url');
  assert.throws(() => verify(keys, sign(keys, { nonce: unissued }), newStore()), /nonce is unknown/);
});

test('an unverified payload is not treated as verified', () => {
  assert.equal(isVerifiedAssertion({ sub: 'x' }), false);
});
