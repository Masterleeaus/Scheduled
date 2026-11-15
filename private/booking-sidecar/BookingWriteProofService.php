<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 44: assert that persisted Cleanto records match the transaction input. */
final class BookingWriteProofService { public static function check(array $row,array $service):void { foreach(['service_id','method_id','method_unit_id'] as $k){ if(BookingWriteProofId::check($row[$k]??null)!==$service[$k])throw new \DomainException('Service mismatch: '.$k);}if(!isset($row['method_unit_qty'])||!is_scalar($row['method_unit_qty'])||!preg_match('/^[0-9]+(?:\.0+)?$/D',(string)$row['method_unit_qty'])||(float)$row['method_unit_qty']!==(float)$service['quantity'])throw new \DomainException('Quantity mismatch');} }
