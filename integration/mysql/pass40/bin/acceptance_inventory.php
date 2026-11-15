<?php
declare(strict_types=1);
/** Clear prerequisite list; never claims acceptance without executed evidence. */
final class Pass40Inventory {
 public static function required():array {return [
  'mysql_driver','isolated_test_database','inno_db_tables','cleanto_seed_data','priced_service_fixture','staff_availability_fixture','actual_booking_commit','independent_readback','two_process_conflict','idempotent_replay','cancellation','rescheduling'
 ];}
 public static function evaluate(array $evidence):array { $missing=[];foreach(self::required() as $k){if(($evidence[$k]??null)!==true)$missing[]=$k;}return ['ready'=>count($missing)===0,'missing'=>$missing]; }
}
