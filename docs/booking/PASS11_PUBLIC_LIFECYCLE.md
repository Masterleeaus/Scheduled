# Pass 11 — Public booking customer experience / operational boundary

The existing booking widget is available across every embedded website template. Pass 11 clarifies its two server-controlled operational states: availability-only, or tenant-authorised reservation submissions. A fresh verified booking reference is required before claiming a confirmed appointment. Payment is never shown as settled by this widget.

New features: visible mode indication, start-again control, booking-local timezone date validation, helpful empty-time state, stronger template/accessibility wording and an admin-preview hard gate. None of these changes enables booking writes.

## Live staging acceptance (not executed in this environment)
1. Confirm server tenant readiness and mysql PDO via `php artisan booking:verify-tenants`.
2. Preview as logged-in admin: verify booking submit flag remains false even for eligible tenant.
3. Preview as public tenant with commits disabled: availability works; no POST to `/titan-booking/checkout/submit`.
4. Enable booking commits only after database acceptance. Public browser: select valid service, staff, date and time. Enter customer fields and submit; verify the persisted booking reference and protected slot in MariaDB.
5. Repeated customer submission must not duplicate the booking. Disconnect after request: use read-only status recovery, never auto-resubmit.
6. Test visitor in US timezone booking an Australia/Melbourne appointment around midnight and DST boundaries.
7. Verify cancellation/rescheduling and notification workflows separately: those are not implemented by this pass.

No claims of online payments or real end-to-end availability are warranted until staging tests pass.
