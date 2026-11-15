<?php
declare(strict_types=1);
namespace App\Booking\Public;
final class PublicStaffMembership {
 public static function assertMember(array $catalogue,int $id):void {
  if(($catalogue['preview_only']??null)!==true||($catalogue['booking_writes_enabled']??null)!==false)throw new \RuntimeException('Catalogue cannot be trusted');
  if(!isset($catalogue['staff'])||!is_array($catalogue['staff']))throw new \RuntimeException('Missing cleaner catalogue');
  $found=0;foreach($catalogue['staff'] as $row){if(!is_array($row)||!is_int($row['id']??null))throw new \RuntimeException('Malformed cleaner catalogue');if($row['id']===$id)$found++;}
  if($found!==1)throw new \InvalidArgumentException('Cleaner not available for this business');
 }
}
