<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Report success is always infrastructure-only; never booking production certification. */
final class AcceptanceDecision {
 public static function evaluate(array $schema,array $locks,array $transaction,array $session):array {
  $errors=[];
  if(!$schema['valid'])$errors[]='schema';
  if(($locks['isolated']??false)!==true||($locks['released']??false)!==true)$errors[]='lock_contention';
  if(($transaction['rollback']??false)!==true)$errors[]='rollback';
  if(($session['safe']??false)!==true)$errors[]='session';
  return ['infrastructure_ready'=>count($errors)===0,'failures'=>$errors,'booking_writes_enabled'=>false,'live_booking_concurrency_verified'=>false];
 }
}
