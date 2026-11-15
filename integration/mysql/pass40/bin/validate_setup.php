<?php
declare(strict_types=1);
/** Offline, non-mutating preflight for acceptance deployment. */
final class Pass40Setup {
 public static function check(array $env, array $drivers):array {
  $errors=[];
  if (!in_array('mysql',$drivers,true)) $errors[]='pdo_mysql extension missing';
  if (($env['BOOKING_TEST_DB_NAME']??'')!=='titan_booking_test_acceptance')$errors[]='Wrong disposable database';
  if (!in_array(($env['BOOKING_TEST_DB_HOST']??''),['mysql-test','127.0.0.1','localhost'],true))$errors[]='Unapproved test host';
  if (($env['BOOKING_TEST_HOST']??$env['BOOKING_TEST_DB_HOST']??'')!==($env['BOOKING_TEST_DB_HOST']??''))$errors[]='Conflicting test host aliases';
  if (($env['BOOKING_TEST_DATABASE']??$env['BOOKING_TEST_DB_NAME']??'')!==($env['BOOKING_TEST_DB_NAME']??''))$errors[]='Conflicting test database aliases';
  if (($env['BOOKING_TEST_USER']??$env['BOOKING_TEST_DB_USER']??'')!==($env['BOOKING_TEST_DB_USER']??''))$errors[]='Conflicting test user aliases';
  if (empty($env['BOOKING_TEST_DB_USER']))$errors[]='Missing test user';
  if (isset($env['BOOKING_TEST_PASSWORD'])&&$env['BOOKING_TEST_PASSWORD']!==($env['BOOKING_TEST_DB_PASSWORD']??null))$errors[]='Conflicting test password aliases';
  if (empty($env['BOOKING_TEST_DB_PASSWORD']))$errors[]='Missing test password';
  return ['ready'=>count($errors)===0,'errors'=>$errors];
 }
}
if(realpath($_SERVER['SCRIPT_FILENAME']??'')===__FILE__){
 $result=Pass40Setup::check($_ENV+getenv(),PDO::getAvailableDrivers());
 echo json_encode($result,JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR).PHP_EOL;
 exit($result['ready']?0:77);
}
