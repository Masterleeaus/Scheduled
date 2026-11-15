# Pass 32: Public read-only availability

- Registers POST `/titan-booking/availability` protected by Laravel throttling.
- Resolves owner from a server-verified hostname; checks selected staff is in that owner's Cleanto catalogue.
- Sends only signed `availability` request through the existing private sidecar; keeps all write operations disabled.
- Validates calendar date, duration, timezone and request fields; strips unknown/private data from the public result.
- Updates the public widget to query availability without requiring an owner CSRF token.
- Uses existing Cleanto availability calculation and schedule tables without editing Cleanto original files.
- Public availability is STAFF-only and does not verify service pricing, quotes, or payment.
- Not tested against a live Laravel or MySQL installation. No appointment is created.
