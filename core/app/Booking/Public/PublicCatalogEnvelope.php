<?php
namespace App\Booking\Public;
final class PublicCatalogEnvelope {
 public static function build(array $data):array {PublicBookingFlags::guard($data);return PublicCatalogueSanitizer::onlyAllowed($data);}
}
