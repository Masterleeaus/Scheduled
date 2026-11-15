<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Reject missing, ambiguous or malformed persisted customer-order links. */
final class LifecycleClientOrderRow {
 public static function parse(array $r):array {
  $out=[];
  foreach(['id','order_id','order_duration'] as $k){
   $out[$k]=LifecycleLegacyId::parse($r[$k]??null,$k);
  }
  if($out['order_duration']>43200)throw new \RuntimeException('Order duration exceeds supported maximum');
  foreach(['client_name','client_email','client_phone'] as $k){
   if(!is_string($r[$k]??null)||trim($r[$k])==='')throw new \RuntimeException('Missing customer order field: '.$k);
  }
  if(!filter_var($r['client_email'],FILTER_VALIDATE_EMAIL))throw new \RuntimeException('Invalid stored customer email');
  $out['email_fingerprint']=hash('sha256',strtolower(trim($r['client_email'])));
  return $out;
 }
}
