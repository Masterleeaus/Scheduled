# PlusAgency + Cleanto — Pass 93

## Scope
Integrated public submission HTTP route with a default-off, server-owned commit-executor contract. Existing trusted Cleanto writer is not auto-bound. Source is not yet certified for public booking.

## Changes
- core/app/Booking/Public/Checkout/CheckoutSubmissionGate.php
- core/app/Booking/Public/Checkout/CheckoutSubmissionExecutor.php
- core/app/Http/Controllers/Front/BookingPublicSubmissionController.php
- core/routes/web.php
- core/config/booking_internal.php
- core/tests/Booking/pass93_submission_gate_test.php

## Verification
- PHP syntax: passed for all changed PHP files.
- Focused execution test: passed (7 source/runtime safety assertions).
- A genuine MariaDB booking commit, concurrency acceptance and browser E2E: NOT RUN.
- Pass 58 source gap: unresolved.
- Customer-facing booking writes remain off by default; no executor implementation is registered by this pass.

## Required next action
On an isolated MariaDB-capable deployment: implement and bind CheckoutSubmissionExecutor using the existing trusted CommitCleantoNativeGateway, never from a public request; verify booking/associated order independent readback, rollback, two-customer collision, replay, and only then explicitly permit writes.
