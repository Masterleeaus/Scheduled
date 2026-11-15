import { test } from 'node:test';
import assert from 'node:assert/strict';
import { LinkRegistry, TenantStore, AccessDeniedError } from '../src/tenant.js';
import { signLinkAssertion, verifyLinkAssertion } from '../src/link-assertion.js';
import { NonceStore } from '../src/nonce-store.js';
import { makeKeys, ISSUER, AUDIENCE, KEY_ID } from './helpers.js';

const now = 1_800_000_000;
const keys = makeKeys();

// Issue a real, verified assertion for one membership.
function verifiedFor({ sub, org, lu, la }) {
  const store = new NonceStore({ now: () => now });
  const nonce = store.issue();
  const token = signLinkAssertion({
    privateKey: keys.privateKey, keyId: KEY_ID, issuer: ISSUER, audience: AUDIENCE, sub, org, lu, la, nonce, nowSeconds: now,
  });
  return verifyLinkAssertion({
    token, publicKey: keys.publicKey, keyId: KEY_ID, issuer: ISSUER, audience: AUDIENCE, nonceStore: store, nowSeconds: now,
  });
}

// One Central identity with two memberships, one company each.
const SUB = 'central-identity-1';
const ORG_A = 'logto-org-a';
const ORG_B = 'logto-org-b';

function setup() {
  const links = new LinkRegistry();
  links.link({ sub: SUB, org: ORG_A, lu: 'lu-1', la: 'la-a' });
  links.link({ sub: SUB, org: ORG_B, lu: 'lu-1', la: 'la-b' });
  const store = new TenantStore();
  return { links, store };
}

test('the tenant key comes from the verified org claim', () => {
  const { links } = setup();
  const access = links.resolveAccess(verifiedFor({ sub: SUB, org: ORG_A, lu: 'lu-1', la: 'la-a' }));
  assert.equal(access.tenantKey, ORG_A);
  assert.equal(access.localAccountId, 'la-a');
});

test('two companies are isolated in tenant-scoped storage', () => {
  const { links, store } = setup();
  const accessA = links.resolveAccess(verifiedFor({ sub: SUB, org: ORG_A, lu: 'lu-1', la: 'la-a' }));
  const accessB = links.resolveAccess(verifiedFor({ sub: SUB, org: ORG_B, lu: 'lu-1', la: 'la-b' }));
  store.insert(accessA, { id: 'visit-1' });
  store.insert(accessB, { id: 'visit-2' });

  assert.deepEqual(store.list(accessA).map((r) => r.id), ['visit-1']);
  assert.deepEqual(store.list(accessB).map((r) => r.id), ['visit-2']);
  assert.equal(store.list(accessA)[0].tenant_key, ORG_A);
});

test('storage refuses to run without a resolved tenant', () => {
  const store = new TenantStore();
  assert.throws(() => store.list(undefined), AccessDeniedError);
  assert.throws(() => store.list({ tenantKey: '' }), AccessDeniedError);
  assert.throws(() => store.insert({}, { id: 'x' }), AccessDeniedError);
});

test('a revoked membership loses access while the other membership keeps it', () => {
  const { links } = setup();
  links.revoke(SUB, ORG_B);
  assert.throws(() => links.resolveAccess(verifiedFor({ sub: SUB, org: ORG_B, lu: 'lu-1', la: 'la-b' })), AccessDeniedError);
  assert.equal(links.resolveAccess(verifiedFor({ sub: SUB, org: ORG_A, lu: 'lu-1', la: 'la-a' })).tenantKey, ORG_A);
});

test('an organisation with no local link is denied', () => {
  const { links } = setup();
  assert.throws(() => links.resolveAccess(verifiedFor({ sub: SUB, org: 'logto-org-c', lu: 'lu-1', la: 'la-c' })), AccessDeniedError);
});

test('a link whose local account does not match the assertion is denied', () => {
  const { links } = setup();
  assert.throws(() => links.resolveAccess(verifiedFor({ sub: SUB, org: ORG_A, lu: 'lu-1', la: 'la-b' })), AccessDeniedError);
});

test('an unverified payload cannot resolve tenant access', () => {
  const { links } = setup();
  assert.throws(() => links.resolveAccess({ sub: SUB, org: ORG_A, lu: 'lu-1', la: 'la-a' }), /must be verified/);
});
