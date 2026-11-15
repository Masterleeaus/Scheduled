import { generateKeyPairSync, createPublicKey } from 'node:crypto';

// Test kids (see src/kid-guard.js) are accepted only when NODE_ENV is 'test'.
process.env.NODE_ENV ??= 'test';

export const ISSUER = 'scheduled';
export const AUDIENCE = 'titan-central';
export const KEY_ID = 'test-kid-1';

// Generated in memory per test run. No key material is committed.
export function makeKeys() {
  const { privateKey } = generateKeyPairSync('ed25519');
  return { privateKey, publicKey: createPublicKey(privateKey) };
}

export const baseClaims = (overrides = {}) => ({
  issuer: ISSUER,
  audience: AUDIENCE,
  sub: 'central-identity-1',
  org: 'logto-org-a',
  lu: 'local-user-1',
  la: 'local-account-a',
  nonce: 'test-nonce-0000000000000001',
  nowSeconds: 1_800_000_000,
  ...overrides,
});
