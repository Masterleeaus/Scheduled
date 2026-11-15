<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Test-only customer contact fields must not address real recipients. */
final class LifecycleCustomerSafety { public static function assert(array $create):void {$email=$create['customer']['email']??null; if(!is_string($email)||!str_ends_with(strtolower($email),'@example.invalid'))throw new \DomainException('Lifecycle customer must use @example.invalid test email');if(isset($create['customer']['phone']) && !is_string($create['customer']['phone']))throw new \DomainException('Test phone must be text');} }
