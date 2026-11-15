<?php
declare(strict_types=1);
final class Pass40Fixture {
 public static function inspect(string $filename,string $database):array {
  if(!is_file($filename)||is_link($filename)||filesize($filename)>65536)throw new InvalidArgumentException('Unsafe fixture file');
  $json=file_get_contents($filename);
  if($json===false)throw new RuntimeException('Unreadable fixture');
  $fixture=json_decode($json,true,32,JSON_THROW_ON_ERROR);
  if(!is_array($fixture)||array_is_list($fixture))throw new InvalidArgumentException('Object fixture required');
  if(($fixture['database']??null)!==$database||($fixture['environment']??null)!=='disposable')throw new DomainException('Fixture is not scoped to disposable database');
  if($database!=='titan_booking_test_acceptance')throw new DomainException('Unexpected database');
  if(!isset($fixture['tenant'])||!is_array($fixture['tenant']))throw new InvalidArgumentException('Tenant fixture required');
  return ['valid'=>true,'database'=>$database,'keys'=>array_keys($fixture)];
 }
}
if(realpath($_SERVER['SCRIPT_FILENAME']??'')===__FILE__){
 try{$out=Pass40Fixture::inspect($argv[1]??'',$argv[2]??'');echo json_encode($out,JSON_THROW_ON_ERROR).PHP_EOL;}catch(Throwable $e){fwrite(STDERR,'FAIL: fixture invalid'.PHP_EOL);exit(2);}
}
