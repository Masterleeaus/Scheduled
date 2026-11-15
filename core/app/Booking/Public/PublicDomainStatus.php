<?php
namespace App\Booking\Public;
final class PublicDomainStatus {
 public static function allowed(string $value):bool{return in_array($value,['pending','active','suspended'],true);}
 public static function publicEnabled(string $value,?string $verifiedAt):bool{return $value==='active'&&is_string($verifiedAt)&&$verifiedAt!=='';}
}
