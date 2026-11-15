import { randomBytes } from 'node:crypto';

const DEFAULT_TTL_SECONDS = 300;

/**
 * Single-use nonce storage for link assertions. Each nonce can be consumed once;
 * a second consume, an unknown nonce or an expired nonce all return false.
 *
 * SINGLE-INSTANCE ONLY. This store is process-local and in-memory. Nonces issued by one
 * process cannot be consumed by another, and a restart drops them all. Do not run more than
 * one instance with this store. A multi-instance deployment needs a shared durable store with
 * the same semantics (tracked in the PR notes; see also core/config/central.php).
 */
export class NonceStore {
  #pending = new Map();
  #ttlSeconds;
  #now;

  constructor({ ttlSeconds = DEFAULT_TTL_SECONDS, now = () => Math.floor(Date.now() / 1000) } = {}) {
    if (!Number.isInteger(ttlSeconds) || ttlSeconds <= 0 || ttlSeconds > DEFAULT_TTL_SECONDS) {
      throw new RangeError('ttlSeconds must be an integer from 1 to 300');
    }
    this.#ttlSeconds = ttlSeconds;
    this.#now = now;
  }

  issue() {
    const current = this.#now();
    this.#prune(current);
    const nonce = randomBytes(16).toString('base64url');
    this.#pending.set(nonce, current + this.#ttlSeconds);
    return nonce;
  }

  consume(nonce) {
    if (typeof nonce !== 'string' || nonce === '') {
      return false;
    }
    const expiresAt = this.#pending.get(nonce);
    // Delete before checking expiry so that a nonce is never accepted twice.
    this.#pending.delete(nonce);
    return expiresAt !== undefined && expiresAt > this.#now();
  }

  #prune(current) {
    for (const [nonce, expiresAt] of this.#pending) {
      if (expiresAt <= current) {
        this.#pending.delete(nonce);
      }
    }
  }
}
