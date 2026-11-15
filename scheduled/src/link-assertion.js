import { createPublicKey, sign, verify } from 'node:crypto';
import { PRODUCT_ID_PATTERN } from './config.js';

export const ALG = 'EdDSA';
export const TYP = 'titan-link+jwt';
export const MAX_LIFETIME_SECONDS = 300;

export const FORBIDDEN_CLAIMS = Object.freeze([
  'email',
  'email_verified',
  'name',
  'given_name',
  'family_name',
  'display_name',
  'displayName',
  'preferred_username',
  'username',
  'nickname',
  'phone_number',
]);

const REQUIRED_CLAIMS = Object.freeze(['iss', 'aud', 'sub', 'org', 'lu', 'la', 'nonce', 'iat', 'exp']);
const HEADER_KEYS = Object.freeze(['alg', 'kid', 'typ']);

// Payloads that passed verifyLinkAssertion. Tenant resolution accepts only these.
const verifiedPayloads = new WeakSet();

export class AssertionError extends Error {
  constructor(message) {
    super(message);
    this.name = 'AssertionError';
  }
}

function nonEmptyString(value, name) {
  if (typeof value !== 'string' || value.trim() === '') {
    throw new AssertionError(`${name} must be a non-empty string`);
  }
  return value;
}

function decodeSegment(segment, name) {
  if (typeof segment !== 'string' || !/^[A-Za-z0-9_-]*$/.test(segment)) {
    throw new AssertionError(`${name} is not base64url`);
  }
  try {
    return JSON.parse(Buffer.from(segment, 'base64url').toString('utf8'));
  } catch {
    throw new AssertionError(`${name} is not valid JSON`);
  }
}

function assertPlainObject(value, name) {
  if (value === null || typeof value !== 'object' || Array.isArray(value)) {
    throw new AssertionError(`${name} must be a JSON object`);
  }
}

/**
 * Sign a link assertion as a compact JWS (EdDSA, typ titan-link+jwt).
 * Only the whitelisted identity fields are accepted, so forbidden profile
 * claims cannot be added on the signing side.
 */
export function signLinkAssertion({
  privateKey,
  keyId,
  issuer,
  audience,
  sub,
  org,
  lu,
  la,
  nonce,
  nowSeconds = Math.floor(Date.now() / 1000),
  lifetimeSeconds = MAX_LIFETIME_SECONDS,
}) {
  if (!PRODUCT_ID_PATTERN.test(nonEmptyString(issuer, 'issuer'))) {
    throw new AssertionError('issuer must be a valid product id');
  }
  nonEmptyString(keyId, 'keyId');
  nonEmptyString(audience, 'audience');
  nonEmptyString(sub, 'sub');
  nonEmptyString(org, 'org');
  nonEmptyString(lu, 'lu');
  nonEmptyString(la, 'la');
  nonEmptyString(nonce, 'nonce');
  if (!Number.isInteger(lifetimeSeconds) || lifetimeSeconds <= 0 || lifetimeSeconds > MAX_LIFETIME_SECONDS) {
    throw new AssertionError(`lifetimeSeconds must be an integer from 1 to ${MAX_LIFETIME_SECONDS}`);
  }

  const header = { alg: ALG, typ: TYP, kid: keyId };
  const payload = {
    iss: issuer,
    aud: audience,
    sub,
    org,
    lu,
    la,
    nonce,
    iat: nowSeconds,
    exp: nowSeconds + lifetimeSeconds,
  };
  const signingInput = `${Buffer.from(JSON.stringify(header)).toString('base64url')}.${Buffer.from(JSON.stringify(payload)).toString('base64url')}`;
  const signature = sign(null, Buffer.from(signingInput), privateKey).toString('base64url');
  return `${signingInput}.${signature}`;
}

/** Public JWK for the operator, derived from the private key. Contains no private material. */
export function publicJwk(privateKey, keyId) {
  const jwk = createPublicKey(privateKey).export({ format: 'jwk' });
  return Object.freeze({ kty: jwk.kty, crv: jwk.crv, x: jwk.x, kid: keyId, alg: ALG, use: 'sig' });
}

/**
 * Verify a compact link assertion. Checks the signature first, then the header,
 * claims, lifetime and forbidden claims, and consumes the nonce last.
 * @returns {Record<string, unknown>} the verified payload
 */
export function verifyLinkAssertion({ token, publicKey, keyId, issuer, audience, nonceStore, nowSeconds = Math.floor(Date.now() / 1000) }) {
  if (typeof token !== 'string') {
    throw new AssertionError('token must be a string');
  }
  const parts = token.split('.');
  if (parts.length !== 3) {
    throw new AssertionError('token must have three segments');
  }
  const [encodedHeader, encodedPayload, encodedSignature] = parts;

  const header = decodeSegment(encodedHeader, 'header');
  assertPlainObject(header, 'header');
  if (JSON.stringify(Object.keys(header).sort()) !== JSON.stringify(HEADER_KEYS)) {
    throw new AssertionError('header must contain exactly alg, kid and typ');
  }
  if (header.alg !== ALG) {
    throw new AssertionError('alg must be EdDSA');
  }
  if (header.typ !== TYP) {
    throw new AssertionError('typ must be titan-link+jwt');
  }
  if (header.kid !== keyId) {
    throw new AssertionError('kid does not match a trusted key');
  }

  const signatureBytes = Buffer.from(encodedSignature, 'base64url');
  const signed = verify(null, Buffer.from(`${encodedHeader}.${encodedPayload}`), publicKey, signatureBytes);
  if (!signed) {
    throw new AssertionError('signature verification failed');
  }

  const payload = decodeSegment(encodedPayload, 'payload');
  assertPlainObject(payload, 'payload');

  for (const claim of FORBIDDEN_CLAIMS) {
    if (Object.hasOwn(payload, claim)) {
      throw new AssertionError(`forbidden claim present: ${claim}`);
    }
  }
  for (const claim of REQUIRED_CLAIMS) {
    if (!Object.hasOwn(payload, claim)) {
      throw new AssertionError(`missing claim: ${claim}`);
    }
  }
  if (payload.iss !== issuer) {
    throw new AssertionError('iss does not match this product');
  }
  if (payload.aud !== audience) {
    throw new AssertionError('aud does not match the Central audience');
  }
  for (const claim of ['sub', 'org', 'lu', 'la', 'nonce']) {
    nonEmptyString(payload[claim], claim);
  }
  if (!Number.isInteger(payload.iat) || !Number.isInteger(payload.exp)) {
    throw new AssertionError('iat and exp must be integer seconds');
  }
  if (payload.exp - payload.iat > MAX_LIFETIME_SECONDS || payload.exp <= payload.iat) {
    throw new AssertionError('exp must be after iat and at most 300 seconds later');
  }
  if (payload.iat > nowSeconds) {
    throw new AssertionError('assertion is not yet valid');
  }
  if (payload.exp <= nowSeconds) {
    throw new AssertionError('assertion has expired');
  }
  if (!nonceStore || !nonceStore.consume(payload.nonce)) {
    throw new AssertionError('nonce is unknown, already used or expired');
  }

  verifiedPayloads.add(payload);
  return payload;
}

/** True only for payloads returned by verifyLinkAssertion. */
export function isVerifiedAssertion(payload) {
  return typeof payload === 'object' && payload !== null && verifiedPayloads.has(payload);
}
