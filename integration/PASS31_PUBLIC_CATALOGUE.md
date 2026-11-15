# Pass 31 public catalogue

The public endpoint uses a verified, active `booking_public_domains` mapping from the request hostname to an owner account, then the existing signed, read-only Cleanto catalogue gateway. It never accepts a tenant identifier from the customer. Domains start pending and **must** be verified by a trusted operator before changing status to active. Registering a domain or authenticating its ownership is not implemented by this pass.

The endpoint is **GET /titan-booking/catalogue**, rate-limited, no-store, and never enables booking writes. It is intentionally independent of public customer booking submission. The widget displays options, but has no public price/time reservation service until MySQL lifecycle testing and safe per-tenant customer APIs are complete. It may continue showing the contact-business message for availability previews.

Important deployment: verify reverse proxy trusted host configuration, migrate `booking_public_domains`, and register only proven business-controlled hostnames through an authorised admin workflow. Any unexpected host returns unavailable.
