<?php
namespace App\Booking\Public;
final class PublicCatalogProjection {
 public static function fromOwnerCatalogue(array $data):array {
  if(($data['preview_only']??null)!==true||($data['booking_writes_enabled']??null)!==false)throw new \RuntimeException('Unsafe catalogue state');
  foreach(['services','methods','units','staff'] as $key){if(!isset($data[$key])||!is_array($data[$key])||!array_is_list($data[$key]))throw new \RuntimeException('Incomplete public catalogue');}
  return ['services'=>$data['services'],'methods'=>$data['methods'],'units'=>$data['units'],'staff'=>$data['staff'],'preview_only'=>true,'booking_writes_enabled'=>false];
 }
}
