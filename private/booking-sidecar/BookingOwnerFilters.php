<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Validated, parameterised filters for the tenant's existing Cleanto bookings. */
final class BookingOwnerFilters {
 public static function from(array $input):array {
  $out=[];
  foreach(['from','to'] as $k){
   $v=trim((string)($input[$k]??''));
   if($v!=='' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$v) || !\DateTimeImmutable::createFromFormat('!Y-m-d',$v) || \DateTimeImmutable::createFromFormat('!Y-m-d',$v)->format('Y-m-d')!==$v))throw new \InvalidArgumentException('Invalid booking date');
   if($v!=='')$out[$k]=$v;
  }
  if(isset($out['from'],$out['to']) && $out['from']>$out['to'])throw new \InvalidArgumentException('Inverted date range');
  if(isset($out['from'],$out['to']) && (new \DateTimeImmutable($out['from']))->diff(new \DateTimeImmutable($out['to']))->days>366)throw new \InvalidArgumentException('Maximum range is one year');
  $status=trim((string)($input['status']??''));
  if($status!==''){
   if(!preg_match('/^[A-Za-z0-9_-]{1,12}$/D',$status))throw new \InvalidArgumentException('Invalid booking status');
   $out['status']=$status;
  }
  $staff=$input['staff_id']??null;
  if($staff!==null && $staff!==''){
   $id=filter_var($staff,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>2147483647]]);
   if($id===false)throw new \InvalidArgumentException('Invalid staff ID');
   $out['staff_id']=(int)$id;
  }
  return $out;
 }
 public static function where(array $filters,array &$params):string {
  $sql='';
  if(isset($filters['from'])){$sql.=' AND b.booking_date_time >= ?';$params[]=$filters['from'].' 00:00:00';}
  if(isset($filters['to'])){$sql.=' AND b.booking_date_time < ?';$params[]=(new \DateTimeImmutable($filters['to']))->modify('+1 day')->format('Y-m-d').' 00:00:00';}
  if(isset($filters['status'])){$sql.=' AND b.booking_status = ?';$params[]=$filters['status'];}
  // Exact comma-separated ID membership; never use substring matching (1 must not match 11).
  if(isset($filters['staff_id'])){$sql.=" AND FIND_IN_SET(?, REPLACE(b.staff_ids, ' ', '')) > 0";$params[]=(string)$filters['staff_id'];}
  return $sql;
 }
}
