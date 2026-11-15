<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
/** Fail closed on PDO methods which can return false without throwing. */
final class RedeemTransaction {
 public static function run(\PDO $db,callable $action):mixed {
  if($db->inTransaction())throw new \LogicException('Nested claim transaction');
  if($db->beginTransaction()!==true)throw new \RuntimeException('Booking transaction could not start');
  $committed=false;
  try{
   $result=$action();
   if(!$db->inTransaction())throw new \RuntimeException('Booking transaction was unexpectedly closed');
   if($db->commit()!==true)throw new \RuntimeException('Booking transaction commit failed; outcome uncertain');
   $committed=true;
   return $result;
  }catch(\Throwable $primary){
   if(!$committed && $db->inTransaction()){
    try{if($db->rollBack()!==true)throw new \RuntimeException('Rollback returned false');}
    catch(\Throwable $rollback){throw new \RuntimeException('Booking rollback failed; outcome uncertain',0,$primary);}
   }
   throw $primary;
  }
 }
}
