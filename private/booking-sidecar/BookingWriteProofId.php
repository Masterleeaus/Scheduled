<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 44: assert that persisted Cleanto records match the transaction input. */
final class BookingWriteProofId { public static function check(mixed $value):int { if(!is_int($value) && !(is_string($value)&&preg_match('/^[1-9][0-9]*$/D',$value)))throw new \DomainException('Invalid persisted booking id'); $n=(int)$value;if($n<1||$n>2147483647)throw new \DomainException('Persisted booking id out of bounds');return $n;} }
