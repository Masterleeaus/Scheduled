<?php
namespace App\Booking\Public;
final class PublicHost {
 public static function normalize(string $host):string {
  if(preg_match('/[\x00-\x1f\x7f]/',$host))throw new \InvalidArgumentException('Invalid public booking domain');
  $host=strtolower(rtrim(trim($host),'.'));
  if(strlen($host)<4||strlen($host)>253||str_contains($host,':')||str_contains($host,'/')||str_contains($host,'\\')||preg_match('/[\x00-\x20\x7f]/',$host)||filter_var($host,FILTER_VALIDATE_IP))throw new \InvalidArgumentException('Invalid public booking domain');
  if(!preg_match('/^(?=.{4,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/D',$host))throw new \InvalidArgumentException('Invalid public booking domain');
  return $host;
 }
}
