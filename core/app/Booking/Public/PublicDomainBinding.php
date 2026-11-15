<?php
namespace App\Booking\Public;
final class PublicDomainBinding {
 public static function validate(object $row):array {
  if(!isset($row->owner_user_id,$row->domain,$row->status,$row->verified_at)||$row->status!=='active'||!$row->verified_at|| (int)$row->owner_user_id<1)throw new \RuntimeException('Domain is not verified and active');
  return ['domain'=>PublicHost::normalize((string)$row->domain),'owner_user_id'=>(int)$row->owner_user_id];
 }
}
