<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Block release on missing critical indexes, including unique ledger identity. */
final class BookingSchemaIndexContract {
 public static function required():array {return ['titan_booking_requests'=>['request_key'],'titan_booking_orders'=>['id'],'ct_bookings'=>['id']];}
 public static function absent(array $indexes):array {
  $missing=[];foreach(self::required() as $table=>$cols){foreach($cols as $col){$matched=false;foreach($indexes[$table]??[] as $index){if(($index['column']??null)===$col && ($index['unique']??false)===true){$matched=true;break;}}if(!$matched)$missing[]=$table.'.'.$col;}}
  return $missing;
 }
}
