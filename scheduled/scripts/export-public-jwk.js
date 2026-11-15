// Prints the public JWK and kid for the Central operator. Public key only.
import { loadConfig } from '../src/config.js';
import { publicJwk } from '../src/link-assertion.js';

const config = loadConfig();
process.stdout.write(`${JSON.stringify(publicJwk(config.privateKey, config.keyId), null, 2)}\n`);
