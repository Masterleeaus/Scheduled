<?php
/** Test-mode webhook inbox only; never authorises capture or booking confirmation. */
return [
    'enabled'=>env('BOOKING_STRIPE_WEBHOOK_INBOX_ENABLED',false),
    'mode'=>env('BOOKING_STRIPE_WEBHOOK_MODE','disabled'),
    'webhook_secret'=>env('BOOKING_STRIPE_WEBHOOK_SECRET',''),
];
