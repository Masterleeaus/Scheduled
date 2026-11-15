<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class AdvisoryLockProbe {
 public static function run(\PDO $pdo):array {
  $key='tb:test:'.bin2hex(random_bytes(8));
  $acquired=false;$released=false;
  if((int)CheckedProbeSql::execute($pdo,'SELECT GET_LOCK(?, 0)',[$key])->fetchColumn()!==1)throw new \RuntimeException('Cannot acquire test lock');
  $acquired=true;
  try { if(CheckedProbeSql::execute($pdo,'SELECT IS_USED_LOCK(?)',[$key])->fetchColumn()===false)throw new \RuntimeException('Lock ownership not visible');
  } finally {
    $released=((int)CheckedProbeSql::execute($pdo,'SELECT RELEASE_LOCK(?)',[$key])->fetchColumn()===1);
  }
  if(!$released)throw new \RuntimeException('Could not release test lock');
  return ['acquired'=>$acquired,'released'=>$released];
 }
}
