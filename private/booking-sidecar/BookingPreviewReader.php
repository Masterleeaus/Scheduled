<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class BookingPreviewReader {
 public function __construct(private \PDO $db,private array $verifiedSettings=[]){}
 public function preview(array $input):array {
  $p=BookingPreviewRequest::parse($input);BookingPreviewLimit::enforce($p);
  $settings=BookingPreviewRateAssumptions::settings($this->verifiedSettings);
  BookingPreviewSettings::assertVerified($settings);
  $price=(new BookingPriceReader($this->db))->inspect($p['service']['method_unit_id']);
  $slots=[];
  foreach($p['staff_ids'] as $id){
   $response=(new AvailabilityReader($this->db))->slots(BookingPreviewAvailabilityQuery::forStaff($p,$id));
   if(!is_array($response['slots']??null))throw new \RuntimeException('Missing availability');
   $slots[$id]=$response['slots'];
  }
  return BookingPreviewPlanner::build($input,$price['unit'],$price['rules'],$slots,$settings);
 }
}
