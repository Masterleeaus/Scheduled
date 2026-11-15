<?php
declare(strict_types=1);
require dirname(__DIR__).'/verify_reports.php';
function check102(bool $ok,string $message): void {if(!$ok){fwrite(STDERR,"FAIL: ".$message."\n");exit(1);}}
$infra=['scope'=>'database_infrastructure_only','decision'=>['infrastructure_ready'=>true,'booking_writes_enabled'=>false], 'booking_creation_verified'=>false,'schema'=>['valid'=>true],'lock_contention'=>['isolated'=>true,'released'=>true],'transaction'=>['rollback'=>true]];
$life=['scope'=>'disposable_mysql_test_database','production_booking_writes_authorized'=>false,'evidence'=>['real_insert'=>true,'idempotency'=>true,'sequential_overlap'=>true,'reschedule'=>true,'cancel'=>true],'details'=>[]];
$race=['scope'=>'disposable_mysql_test_database','production_booking_writes_authorized'=>false,'actual_two_process_attempts'=>true,'one_winner_verified'=>true,'database'=>'titan_booking_test_acceptance','database_evidence'=>['persisted'=>true,'overlap_scan_passed'=>true,'overlapping_booking_count'=>1]];
$checks=0;
$result=Pass102EvidenceGate::verify($infra,$life,$race);
check102($result['production_release_authorized']===false,'must never certify production');$checks++;
$invalid=[
    ['infra',['schema','valid'],false],
    ['infra',['decision','infrastructure_ready'],false],
    ['infra',['booking_creation_verified'],true],
    ['infra',['lock_contention','released'],false],
    ['infra',['transaction','rollback'],false],
    ['life',['evidence','real_insert'],false],
    ['life',['evidence','idempotency'],false],
    ['life',['evidence','sequential_overlap'],false],
    ['life',['evidence','reschedule'],false],
    ['life',['evidence','cancel'],false],
    ['life',['production_booking_writes_authorized'],true],
    ['race',['actual_two_process_attempts'],false],
    ['race',['one_winner_verified'],false],
    ['race',['database_evidence','persisted'],false],
    ['race',['database_evidence','overlap_scan_passed'],false],
    ['race',['database_evidence','overlapping_booking_count'],2],
    ['race',['database'],'production'],
    ['race',['production_booking_writes_authorized'],true]
];
foreach ($invalid as [$target,$path,$value]) {
    $a=$infra;$b=$life;$c=$race;
    $ref=&${$target==='infra'?'a':($target==='life'?'b':'c')};
    if (count($path)===1) $ref[$path[0]]=$value;
    else $ref[$path[0]][$path[1]]=$value;
    unset($ref);
    $failed=false;try{Pass102EvidenceGate::verify($a,$b,$c);}catch(RuntimeException $ex){$failed=true;}
    check102($failed,'negative evidence case '.json_encode($path));$checks++;
}
$folder=sys_get_temp_dir().'/pa102_'.bin2hex(random_bytes(6));mkdir($folder,0700);
try {
    file_put_contents($folder.'/ok.json',json_encode($infra));
    check102(Pass102EvidenceGate::decode($folder.'/ok.json')['scope']==='database_infrastructure_only','JSON decoding');$checks++;
    file_put_contents($folder.'/bad.json','{');
    $fail=false;try{Pass102EvidenceGate::decode($folder.'/bad.json');}catch(Throwable $e){$fail=true;}
    check102($fail,'malformed JSON fails');$checks++;
    $fail=false;try{Pass102EvidenceGate::decode($folder.'/missing.json');}catch(Throwable $e){$fail=true;}
    check102($fail,'missing JSON fails');$checks++;
} finally {foreach(glob($folder.'/*') as $file)unlink($file);rmdir($folder);}
echo 'PASS102 evidence regression: '.$checks.'/'.$checks." passed (synthetic only)\n";
