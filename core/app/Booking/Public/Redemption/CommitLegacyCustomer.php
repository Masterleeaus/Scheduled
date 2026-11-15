<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Trusted adapter: convert a validated customer object, never a public authority claim. */
final class CommitLegacyCustomer {
 public static function map(array $customer):array {
  $allowed=['name','email','phone'];
  foreach($customer as $key=>$_)if(!in_array($key,$allowed,true))throw new \InvalidArgumentException('Unexpected customer field');
  $name=$customer['name']??null; $email=$customer['email']??null; $phone=$customer['phone']??null;
  if(!is_string($name)||strlen(trim($name))<1||strlen($name)>120)throw new \InvalidArgumentException('Invalid customer name');
  if(!is_string($email)||strlen($email)>254||!filter_var($email,FILTER_VALIDATE_EMAIL))throw new \InvalidArgumentException('Invalid customer email');
  if(!is_string($phone)||!preg_match('/^\+?[0-9 ()-]{7,24}$/D',$phone))throw new \InvalidArgumentException('Invalid customer phone');
  return ['name'=>trim($name),'email'=>strtolower($email),'phone'=>trim($phone)];
 }
}
