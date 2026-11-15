<?php
 declare(strict_types=1);
 namespace App\Booking\Public\Checkout;
 use App\Booking\Public\Redemption\CommitCustomerIdentityPolicy;
 use Titan\BookingSidecar\BookingPersonalInfo;
 use App\Booking\Public\Redemption\CommitCleantoLoader;
 /** Data-only bridge: a validated form does not authorize a booking or payment. */
 final class CheckoutNativeCustomer {
     public static function map(array $raw): array {
         $customer=CheckoutCustomer::parse($raw);
         $identity=CommitCustomerIdentityPolicy::requireSafe([
             'name'=>trim($customer['first_name'].' '.$customer['last_name']),
             'email'=>$customer['email'], 'phone'=>$customer['phone'],
         ]);
         // Cleanto lives outside Laravel Composer PSR-4. Register its private loader
         // before the first sidecar class reference.
         if (!class_exists(CommitCleantoLoader::class)) {
             require_once __DIR__.'/../Redemption/CommitCleantoLoader.php';
         }
         CommitCleantoLoader::register(dirname(__DIR__, 5).'/private/booking-sidecar');
         $personalInfo=BookingPersonalInfo::parse([
             'address'=>$customer['address'], 'city'=>$customer['city'],
             'state'=>$customer['state'], 'zip'=>$customer['zip'],
             'notes'=>$customer['notes'],
         ]);
         // Original Cleanto column bounds; never truncate silently.
         if (strlen($identity['name'])>200 || strlen($identity['email'])>200 || strlen($identity['phone'])>15) {
             throw new \InvalidArgumentException('Customer exceeds original booking column bounds');
         }
         return ['identity'=>$identity,'personal_info'=>$personalInfo];
     }
 }
