import { createPrivateKey } from 'node:crypto';
import { assertKeyIdAllowed } from './kid-guard.js';

export class ConfigError extends Error {
  constructor(message) {
    super(message);
    this.name = 'ConfigError';
  }
}

export const PRODUCT_ID_PATTERN = /^[a-z][a-z0-9_-]*$/;

const REQUIRED = Object.freeze([
  'SCHEDULED_CENTRAL_ORIGIN',
  'SCHEDULED_CENTRAL_AUDIENCE',
  'SCHEDULED_PRODUCT_ID',
  'SCHEDULED_LINK_KEY_ID',
  'SCHEDULED_LINK_PRIVATE_KEY_PEM',
]);

function isLocalhost(hostname) {
  return hostname === 'localhost' || hostname === '127.0.0.1';
}

/**
 * Load and validate configuration. Reports every missing variable at once and
 * never echoes secret values in error messages.
 * @param {Record<string, string | undefined>} env
 */
export function loadConfig(env = process.env) {
  const missing = REQUIRED.filter((name) => !env[name] || env[name].trim() === '');
  if (missing.length > 0) {
    throw new ConfigError(`Missing required environment variables: ${missing.join(', ')}`);
  }

  assertKeyIdAllowed(env.SCHEDULED_LINK_KEY_ID.trim(), env);

  const productId = env.SCHEDULED_PRODUCT_ID.trim();
  if (!PRODUCT_ID_PATTERN.test(productId)) {
    throw new ConfigError('SCHEDULED_PRODUCT_ID must match ^[a-z][a-z0-9_-]*$');
  }

  let origin;
  try {
    origin = new URL(env.SCHEDULED_CENTRAL_ORIGIN.trim());
  } catch {
    throw new ConfigError('SCHEDULED_CENTRAL_ORIGIN must be a valid URL');
  }
  const secure = origin.protocol === 'https:' || (origin.protocol === 'http:' && isLocalhost(origin.hostname));
  if (!secure || origin.pathname !== '/' || origin.search || origin.hash) {
    throw new ConfigError('SCHEDULED_CENTRAL_ORIGIN must be an https origin with no path, query or fragment');
  }

  const pem = env.SCHEDULED_LINK_PRIVATE_KEY_PEM.replace(/\\n/g, '\n');
  let privateKey;
  try {
    privateKey = createPrivateKey(pem);
  } catch {
    throw new ConfigError('SCHEDULED_LINK_PRIVATE_KEY_PEM is not a valid PEM private key');
  }
  if (privateKey.type !== 'private' || privateKey.asymmetricKeyType !== 'ed25519') {
    throw new ConfigError('SCHEDULED_LINK_PRIVATE_KEY_PEM must be an Ed25519 private key');
  }

  return Object.freeze({
    centralOrigin: origin.origin,
    audience: env.SCHEDULED_CENTRAL_AUDIENCE.trim(),
    productId,
    keyId: env.SCHEDULED_LINK_KEY_ID.trim(),
    privateKey,
  });
}
