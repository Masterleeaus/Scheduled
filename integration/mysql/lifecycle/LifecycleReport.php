<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Evidence report explicitly distinguishes direct DB effects from still-missing production proof. */
final class LifecycleReport { public static function from(LifecycleEvidence $e,array $details=[]):array {return ['scope'=>'disposable_mysql_test_database','evidence'=>$e->export(),'production_booking_writes_authorized'=>false,'full_concurrency_certified'=>false,'details'=>$details];} }
