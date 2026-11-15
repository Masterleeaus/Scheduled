<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout;
/** Read-only Cleanto accounting evidence; it is NOT provider settlement proof. */
final class CheckoutPaymentRecord {
 /** Cleanto stores DOUBLEs, so trust only conventional positive currency values. */
 private static function validAmount(mixed $value):bool {
  if (!is_int($value) && !is_string($value) && !is_float($value)) return false;
  // PHP may stringify a database DOUBLE using exponent notation. In that case
  // the precision is not independently provable: classify as ambiguous.
  $text=(string)$value;
  if (!preg_match('/^(?:0|[1-9][0-9]{0,11})(?:\.[0-9]{1,2})?$/D', $text)) return false;
  [$whole,$fraction]=array_pad(explode('.',$text,2),2,'');
  return (int)$whole>0 || (int)str_pad($fraction,2,'0')>0;
 }
 private static function validDate(mixed $value):bool {
  if (!is_string($value) || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D',$value)) return false;
  [$y,$m,$d]=array_map('intval',explode('-',$value));
  return checkdate($m,$d,$y);
 }
 public static function inspect(\PDO $db,int $orderId):array {
  if($orderId<1)throw new \InvalidArgumentException('Invalid order reference');
  if($db->inTransaction())throw new \RuntimeException('Payment evidence requires isolated read');
  $stmt=$db->prepare('SELECT id,order_id,payment_status,payment_method,transaction_id,amount,net_amount,payment_date FROM ct_payments WHERE order_id=? ORDER BY id ASC LIMIT 101');
  if($stmt===false || $stmt->execute([$orderId])!==true)throw new \RuntimeException('Payment evidence query failed');
  $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
  if(!is_array($rows)||count($rows)>100)throw new \RuntimeException('Payment evidence ambiguous');
  return self::classify($rows,$orderId);
 }
 public static function classify(array $rows,int $orderId):array {
  if($orderId<1)throw new \InvalidArgumentException('Invalid order reference');
  if(count($rows)>100)throw new \RuntimeException('Payment evidence ambiguous');
  if(!$rows)return ['record_status'=>'none','record_count'=>0,'payment_settled'=>false];
  $states=[];$ids=[];$transactionRefs=[];$ambiguous=false;
  foreach($rows as $row){
   if(!is_array($row)||!ctype_digit((string)($row['id']??''))||(int)$row['id']<1 ||
      (string)($row['order_id']??'')!==(string)$orderId || isset($ids[(string)$row['id']]))
    throw new \RuntimeException('Invalid Cleanto payment row linkage');
   $ids[(string)$row['id']]=true;
   // These are accounting observations, not provider receipts. A malformed
   // amount or conflicting transaction reference must never appear complete.
   foreach (['amount','net_amount'] as $moneyField) {
    if (!self::validAmount($row[$moneyField]??null)) $ambiguous=true;
   }
   if (!self::validDate($row['payment_date']??null)) $ambiguous=true;
   $method=$row['payment_method']??null;
   if (!is_string($method) || trim($method)==='') $ambiguous=true;
   $reference=$row['transaction_id']??null;
   if (!is_string($reference)) $ambiguous=true;
   elseif ($reference!=='') {
    if (isset($transactionRefs[$reference])) $ambiguous=true;
    $transactionRefs[$reference]=true;
   }
   $status=$row['payment_status']??null;
   if(!is_string($status)||!in_array($status,['Pending','Completed'],true))$ambiguous=true;
   else $states[$status]=true;
  }
  $state=$ambiguous?'ambiguous':(count($states)>1?'mixed':(isset($states['Completed'])?'completed_recorded':'pending'));
  // Cleanto's Completed label can also represent offline/manual payments.
  // Never use this record alone to authorize another charge or assert settlement.
  return ['record_status'=>$state,'record_count'=>count($rows),'payment_settled'=>false];
 }
}
