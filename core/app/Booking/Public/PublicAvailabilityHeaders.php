<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicAvailabilityHeaders {
 public static function apply($response){return $response->header('Cache-Control','private, no-store, max-age=0')->header('X-Content-Type-Options','nosniff')->header('Vary','Host')->header('X-Booking-Mode','preview-only');}
}
