import { AssertionError, isVerifiedAssertion } from './link-assertion.js';

export class AccessDeniedError extends Error {
  constructor(message = 'access denied') {
    super(message);
    this.name = 'AccessDeniedError';
  }
}

const linkKey = (sub, org) => `${sub}\u0000${org}`;

/**
 * Local links keyed by Central identity (sub) and organisation (org). Each
 * membership gets its own local account. Central is the source of truth for
 * membership, so a revoked membership must be revoked here as well.
 */
export class LinkRegistry {
  #links = new Map();

  link({ sub, org, lu, la }) {
    this.#links.set(linkKey(sub, org), { lu, la, active: true });
  }

  revoke(sub, org) {
    const entry = this.#links.get(linkKey(sub, org));
    if (entry) {
      entry.active = false;
    }
  }

  /**
   * Resolve tenant access from a verified assertion. The tenant key comes only
   * from the verified org claim, never from user input.
   * @returns {{ tenantKey: string, localUserId: string, localAccountId: string }}
   */
  resolveAccess(payload) {
    if (!isVerifiedAssertion(payload)) {
      throw new AssertionError('assertion must be verified before tenant resolution');
    }
    const entry = this.#links.get(linkKey(payload.sub, payload.org));
    if (!entry || !entry.active || entry.lu !== payload.lu || entry.la !== payload.la) {
      throw new AccessDeniedError();
    }
    return Object.freeze({ tenantKey: payload.org, localUserId: entry.lu, localAccountId: entry.la });
  }
}

/**
 * Tenant-scoped storage. Every read and write takes a resolved access object,
 * so no query can run without a tenant key.
 */
export class TenantStore {
  #rows = [];

  insert(access, row) {
    if (!access || typeof access.tenantKey !== 'string' || access.tenantKey === '') {
      throw new AccessDeniedError();
    }
    const stored = Object.freeze({ ...row, tenant_key: access.tenantKey });
    this.#rows.push(stored);
    return stored;
  }

  list(access) {
    if (!access || typeof access.tenantKey !== 'string' || access.tenantKey === '') {
      throw new AccessDeniedError();
    }
    return this.#rows.filter((row) => row.tenant_key === access.tenantKey);
  }
}
