# Pass 33 — Preview-only public booking intent

Adds immutable, HMAC-signed 10-minute customer booking intents bound to the verified public domain and business owner. No appointment writes, no payment claim, no tenant supplied by client. Does not implement replay storage; the token MUST NOT be accepted for booking writes without a one-time redemption ledger, fresh availability checks, and database-backed acceptance tests.

Secrets: set TITAN_BOOKING_INTENT_SECRET (random 32+ characters) outside source control. Rate limit is defence-in-depth; review trusted reverse-proxy host handling before deployment.

Original Cleanto files unchanged. MySQL-backed booking creation remains disabled.
