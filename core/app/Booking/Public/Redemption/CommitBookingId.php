<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitBookingId { public static function parse(mixed $value):int { if(!is_int($value) && !(is_string($value)&&preg_match('/^[1-9][0-9]*$/D',$value)))throw new \InvalidArgumentException('Invalid persisted booking id'); $id=filter_var($value,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);if($id===false)throw new \InvalidArgumentException('Invalid persisted booking id');return (int)$id; }}
