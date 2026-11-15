<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyAcceptanceReport {
 public static function make(array $outcome,string $database):array {if(($outcome['passed']??false)!==true)throw new \DomainException('Unproven concurrency');return ['scope'=>'disposable_mysql_test_database','database'=>$database,'actual_two_process_attempts'=>true,'one_winner_verified'=>true,'production_booking_writes_authorized'=>false,'details'=>$outcome];}
}
