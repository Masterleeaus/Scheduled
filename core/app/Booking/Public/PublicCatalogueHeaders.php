<?php
namespace App\Booking\Public;
final class PublicCatalogueHeaders {
 public static function apply($response){return $response->header('Cache-Control','private, no-store, max-age=0')->header('X-Content-Type-Options','nosniff')->header('Vary','Host');}
}
