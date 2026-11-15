<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Return safely bounded diagnostic output without leaking credentials or PII. */
final class LifecycleFailureRedactor { public static function message(\Throwable $error):string {$m=RedactedFailure::message($error);$m=preg_replace('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i','[email]',$m);return substr((string)$m,0,300);} }
