<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
use InvalidArgumentException;
/** Cleanto's client_phone column is varchar(15), so reject overflow instead of truncating. */
final class BookingCustomer {
 public static function parse(array $raw):array {
  $name=trim((string)($raw['name']??''));$email=trim((string)($raw['email']??''));$phone=trim((string)($raw['phone']??''));
  if($name===''||strlen($name)>200||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>200)throw new InvalidArgumentException('Invalid customer');
  if(!preg_match('/^\+?[0-9 ()-]{5,15}$/D',$phone)||strlen($phone)>15)throw new InvalidArgumentException('Invalid customer phone');
  if(preg_match('/[\x00-\x1F\x7F]/',$name.$email.$phone))throw new InvalidArgumentException('Invalid customer control character');
  return ['name'=>$name,'email'=>$email,'phone'=>$phone];
 }
}
