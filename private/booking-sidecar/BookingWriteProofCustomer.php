<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 44: assert that persisted Cleanto records match the transaction input. */
final class BookingWriteProofCustomer { public static function check(array $row,array $customer):void {foreach(['name'=>'client_name','email'=>'client_email','phone'=>'client_phone'] as $key=>$column){if(!is_string($row[$column]??null)||$row[$column]!==$customer[$key])throw new \DomainException('Persisted customer mismatch');}} }
