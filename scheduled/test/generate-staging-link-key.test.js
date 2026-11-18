import { test, after } from 'node:test';
import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import { createPrivateKey, createPublicKey } from 'node:crypto';
import { existsSync, mkdtempSync, readFileSync, rmSync, statSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { generateStagingLinkKey, isInside } from '../scripts/generate-staging-link-key.js';
import { NonceStore } from '../src/nonce-store.js';
import { signLinkAssertion, verifyLinkAssertion } from '../src/link-assertion.js';

const SCRIPT = fileURLToPath(new URL('../scripts/generate-staging-link-key.js', import.meta.url));
const REPO_ROOT = fileURLToPath(new URL('../..', import.meta.url));
const KID = 'scheduled-staging-test-run';

const scratch = mkdtempSync(join(tmpdir(), 'link-key-'));
after(() => rmSync(scratch, { recursive: true, force: true }));

// Only the variables the script needs. NODE_ENV is absent, so test-vector kids are refused.
const cli = (args) => spawnSync(process.execPath, [SCRIPT, ...args], {
  env: { PATH: process.env.PATH },
  encoding: 'utf8',
});

test('writes a private key outside the repository with owner-only permissions', () => {
  const out = join(scratch, 'staging.pem');
  const result = cli(['--out', out, '--kid', KID]);
  assert.equal(result.status, 0, result.stderr);
  assert.equal(statSync(out).mode & 0o777, 0o600);
  const key = createPrivateKey(readFileSync(out, 'utf8'));
  assert.equal(key.asymmetricKeyType, 'ed25519');
});

test('prints only the kid and the public JWK, never the private key', () => {
  const out = join(scratch, 'print-check.pem');
  const result = cli(['--out', out, '--kid', KID]);
  assert.equal(result.status, 0, result.stderr);
  const printed = JSON.parse(result.stdout);
  assert.deepEqual(Object.keys(printed).sort(), ['jwk', 'kid']);
  assert.equal(printed.kid, KID);
  assert.deepEqual(Object.keys(printed.jwk).sort(), ['crv', 'kty', 'x']);
  assert.equal(printed.jwk.kty, 'OKP');
  assert.equal(printed.jwk.crv, 'Ed25519');
  const pem = readFileSync(out, 'utf8');
  const privateD = createPrivateKey(pem).export({ format: 'jwk' }).d;
  assert.doesNotMatch(result.stdout, /PRIVATE|BEGIN|"d"/);
  assert.equal(result.stdout.includes(privateD), false);
});

test('the printed JWK verifies a token signed with the written key, and the nonce is single use', () => {
  const out = join(scratch, 'round-trip.pem');
  const result = cli(['--out', out, '--kid', KID]);
  assert.equal(result.status, 0, result.stderr);
  const { jwk } = JSON.parse(result.stdout);
  const privateKey = createPrivateKey(readFileSync(out, 'utf8'));
  const publicKey = createPublicKey({ key: jwk, format: 'jwk' });

  const nonces = new NonceStore();
  const nonce = nonces.issue();
  const token = signLinkAssertion({
    privateKey,
    keyId: KID,
    issuer: 'scheduled',
    audience: 'titan-central',
    sub: 'central-identity-1',
    org: 'logto-org-a',
    lu: 'local-user-1',
    la: 'local-account-a',
    nonce,
  });
  const payload = verifyLinkAssertion({
    token,
    publicKey,
    keyId: KID,
    issuer: 'scheduled',
    audience: 'titan-central',
    nonceStore: nonces,
  });
  assert.equal(payload.sub, 'central-identity-1');
  assert.throws(
    () => verifyLinkAssertion({ token, publicKey, keyId: KID, issuer: 'scheduled', audience: 'titan-central', nonceStore: nonces }),
    /nonce is unknown, already used or expired/,
  );
});

test('refuses an output path inside the repository and creates nothing there', () => {
  const inRepo = join(REPO_ROOT, 'link-key-should-not-exist.pem');
  const result = cli(['--out', inRepo, '--kid', KID]);
  assert.equal(result.status, 1);
  assert.match(result.stderr, /outside the repository/);
  assert.equal(existsSync(inRepo), false);
});

test('refuses to overwrite an existing key file', () => {
  const out = join(scratch, 'existing.pem');
  writeFileSync(out, 'keep me');
  const result = cli(['--out', out, '--kid', KID]);
  assert.equal(result.status, 1);
  assert.equal(readFileSync(out, 'utf8'), 'keep me');
});

test('refuses a relative output path, a malformed kid and a test-vector kid outside the test environment', () => {
  assert.equal(cli(['--out', 'relative.pem', '--kid', KID]).status, 1);
  assert.equal(cli(['--out', join(scratch, 'bad-kid.pem'), '--kid', 'bad kid!']).status, 1);
  const testKid = cli(['--out', join(scratch, 'test-kid.pem'), '--kid', 'test-kid-1']);
  assert.equal(testKid.status, 1);
  assert.match(testKid.stderr, /reserved for test vectors/);
  assert.equal(existsSync(join(scratch, 'test-kid.pem')), false);
});

test('isInside treats the root and its children as inside, and siblings with a shared prefix as outside', () => {
  assert.equal(isInside('/srv/repo', '/srv/repo'), true);
  assert.equal(isInside('/srv/repo', '/srv/repo/keys/a.pem'), true);
  assert.equal(isInside('/srv/repo', '/srv/repo-keys/a.pem'), false);
  assert.equal(isInside('/srv/repo', '/srv/other/a.pem'), false);
});

test('generateStagingLinkKey refuses a path whose parent resolves inside the repository', () => {
  assert.throws(
    () => generateStagingLinkKey({ outPath: join(REPO_ROOT, 'scheduled', 'x.pem'), kid: KID }),
    /outside the repository/,
  );
});
