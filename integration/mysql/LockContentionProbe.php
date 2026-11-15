<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Proves two distinct MySQL connections cannot simultaneously own one named lock. */
final class LockContentionProbe {
 public static function run(\PDO $first,\PDO $second):array {
  $one=(int)CheckedProbeSql::scalar($first,'SELECT CONNECTION_ID()');
  $two=(int)CheckedProbeSql::scalar($second,'SELECT CONNECTION_ID()');
  if($one===$two)throw new \RuntimeException('Lock test requires distinct sessions');
  $key='tb:test:'.bin2hex(random_bytes(8));$owned=false;
  try {
   $a=CheckedProbeSql::execute($first,'SELECT GET_LOCK(?,0)',[$key]);
   $owned=((int)$a->fetchColumn()===1);if(!$owned)throw new \RuntimeException('First session cannot acquire lock');
   $b=CheckedProbeSql::execute($second,'SELECT GET_LOCK(?,0)',[$key]);
   if((int)$b->fetchColumn()!==0)throw new \RuntimeException('Concurrent lock acquired by second session');
  }finally{
   if($owned){$release=CheckedProbeSql::execute($first,'SELECT RELEASE_LOCK(?)',[$key]);if((int)$release->fetchColumn()!==1)throw new \RuntimeException('First lock release failed');}
  }
  $b=CheckedProbeSql::execute($second,'SELECT GET_LOCK(?,0)',[$key]);
  if((int)$b->fetchColumn()!==1)throw new \RuntimeException('Second session cannot acquire released lock');
  $release=CheckedProbeSql::execute($second,'SELECT RELEASE_LOCK(?)',[$key]);
  if((int)$release->fetchColumn()!==1)throw new \RuntimeException('Second lock release failed');
  return ['isolated'=>true,'released'=>true,'connection_ids'=>[$one,$two]];
 }
}
