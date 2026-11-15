// Prints one key entry for the Central operator, in the shape Central's link-key
// configuration expects (kid, alg, jwk{kty,crv,x}, notBefore, optional notAfter).
// Public key only. Set SCHEDULED_LINK_NOT_BEFORE (RFC 3339 with offset); Central requires it.
import { loadConfig } from '../src/config.js';
import { centralKeyEntry } from '../src/link-assertion.js';

const notBefore = process.env.SCHEDULED_LINK_NOT_BEFORE;
if (!notBefore || notBefore.trim() === '') {
  process.stderr.write('SCHEDULED_LINK_NOT_BEFORE must be set to an RFC 3339 time with an offset\n');
  process.exit(1);
}

const config = loadConfig();
const notAfter = process.env.SCHEDULED_LINK_NOT_AFTER?.trim() || undefined;
const entry = centralKeyEntry({ privateKey: config.privateKey, keyId: config.keyId, notBefore: notBefore.trim(), notAfter });
process.stdout.write(`${JSON.stringify(entry, null, 2)}\n`);
