// Generates an Ed25519 link-signing key pair for STAGING.
//
// The private key (PKCS#8 PEM) is written to --out, an absolute path OUTSIDE this repository,
// with owner-only permissions (0600). The command refuses to overwrite an existing file.
// Only the kid and the public JWK are printed to stdout. Nothing secret is printed.
//
// Usage:
//   node scripts/generate-staging-link-key.js --out /absolute/path/outside/repo/link-staging.pem --kid scheduled-staging-2026-11
//
// Then load the PEM into the secret store or the environment as SCHEDULED_LINK_PRIVATE_KEY_PEM
// (the file contents, or the same text with newlines written as \n). Give the printed kid and
// JWK to the Central operator for registration. Production keys follow the secret-store process.
import { generateKeyPairSync } from 'node:crypto';
import { writeFileSync, realpathSync } from 'node:fs';
import { basename, dirname, isAbsolute, relative, resolve, sep } from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';
import { assertKeyIdAllowed } from '../src/kid-guard.js';
import { KID_PATTERN, publicJwk } from '../src/link-assertion.js';

const REPO_ROOT = fileURLToPath(new URL('../..', import.meta.url));

/** True when target is root itself or lies beneath it. Both paths must already be resolved. */
export function isInside(root, target) {
  const rel = relative(root, target);
  return rel === '' || (rel !== '..' && !rel.startsWith(`..${sep}`) && !isAbsolute(rel));
}

/**
 * Writes a new staging key to outPath and returns the public material only.
 * @returns {{ kid: string, jwk: { kty: string, crv: string, x: string } }}
 */
export function generateStagingLinkKey({ outPath, kid, repoRoot = REPO_ROOT }) {
  if (typeof kid !== 'string' || !KID_PATTERN.test(kid)) {
    throw new Error('kid must be 1 to 64 letters, digits, dots, underscores or hyphens');
  }
  assertKeyIdAllowed(kid);
  if (typeof outPath !== 'string' || !isAbsolute(outPath)) {
    throw new Error('--out must be an absolute path');
  }

  // Resolve the parent through symlinks, so a link cannot point the key back into the repository.
  const target = resolve(realpathSync(dirname(outPath)), basename(outPath));
  if (isInside(realpathSync(repoRoot), target)) {
    throw new Error('--out must be outside the repository');
  }

  const { privateKey } = generateKeyPairSync('ed25519');
  const pem = privateKey.export({ type: 'pkcs8', format: 'pem' });
  // 'wx' fails if the file exists, so an existing key is never replaced.
  writeFileSync(target, pem, { flag: 'wx', mode: 0o600 });

  return { kid, jwk: publicJwk(privateKey) };
}

function parseArgs(argv) {
  const args = {};
  for (let i = 0; i < argv.length; i += 2) {
    const name = argv[i];
    if (name !== '--out' && name !== '--kid') {
      throw new Error(`unknown argument ${name}`);
    }
    if (argv[i + 1] === undefined) {
      throw new Error(`${name} needs a value`);
    }
    args[name.slice(2)] = argv[i + 1];
  }
  if (!args.out || !args.kid) {
    throw new Error('usage: --out <absolute path outside the repository> --kid <kid>');
  }
  return args;
}

if (process.argv[1] && import.meta.url === pathToFileURL(resolve(process.argv[1])).href) {
  try {
    const args = parseArgs(process.argv.slice(2));
    const { kid, jwk } = generateStagingLinkKey({ outPath: args.out, kid: args.kid });
    process.stdout.write(`${JSON.stringify({ kid, jwk }, null, 2)}\n`);
  } catch (error) {
    process.stderr.write(`${error.message}\n`);
    process.exit(1);
  }
}
