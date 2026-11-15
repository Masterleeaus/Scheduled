// Refuses key ids reserved for test vectors outside the test environment.
// Test key material is derived from or generated for tests, so an entry or
// assertion that uses a test kid in production would be forgeable or misleading.
// No dependencies, so every module that handles a kid can import it.
export const TEST_KID_PATTERN = /^(?:test|vector)[-_]/i;

export class KeyIdRefusedError extends Error {
  constructor(message) {
    super(message);
    this.name = 'KeyIdRefusedError';
  }
}

export function isTestEnvironment(env = process.env) {
  return env.NODE_ENV === 'test';
}

/** Throws when a test-vector kid is used outside NODE_ENV=test. Other kids pass through. */
export function assertKeyIdAllowed(keyId, env = process.env) {
  if (typeof keyId !== 'string') {
    return;
  }
  if (TEST_KID_PATTERN.test(keyId) && !isTestEnvironment(env)) {
    throw new KeyIdRefusedError('keyId is reserved for test vectors and is refused outside the test environment');
  }
}
