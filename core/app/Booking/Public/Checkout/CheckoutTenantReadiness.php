<?php
 declare(strict_types=1);
 namespace App\Booking\Public\Checkout;

 /** Server-derived readiness only. No public input may set flags or release evidence. */
 final class CheckoutTenantReadiness {
     public static function eligible(bool $global, bool $bound, bool $mysql, int $owner, $profiles): bool {
         if (!$global || !$bound || !$mysql || $owner < 1 || !is_array($profiles)) return false;
         try {
             CheckoutDeploymentProfile::tenant($owner, $profiles);
             return true;
         } catch (\Throwable $e) {
             return false;
         }
     }

     /** Fail closed on hostname resolution or database exceptions in the public widget. */
     public static function eligibleForHost(bool $global, bool $bound, bool $mysql, callable $ownerLookup, $profiles): bool {
         if (!$global || !$bound || !$mysql || !is_array($profiles) || !$profiles) return false;
         try {
             $owner = $ownerLookup();
             return is_int($owner) && self::eligible($global, $bound, $mysql, $owner, $profiles);
         } catch (\Throwable $e) {
             return false;
         }
     }
 }
