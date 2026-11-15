<?php
namespace App\Booking\Public;
final class PublicHostnameGuard {
 public static function assertKnown(string $requested,string $registered):void{if(!hash_equals(PublicHost::normalize($registered),PublicHost::normalize($requested)))throw new \RuntimeException('Host mismatch');}
}
