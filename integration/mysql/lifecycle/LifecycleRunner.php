<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Run a persisted sequential lifecycle against an explicitly disposable MySQL database. */
final class LifecycleRunner {
 public static function run(\PDO $db,array $cfg,array $fixture):array {
  LifecycleFixtureValidator::validate($fixture);LifecycleWindowEvidence::assertDistinct($fixture);
  LifecycleTestScope::assert($cfg,$fixture);LifecycleCustomerSafety::assert($fixture['create']);
  LifecycleDatabaseReadiness::assert($db,$cfg['database']);
  $reader=MysqlTestConnection::open($cfg);LifecycleConnectionPair::assert($db,$reader);
  $footprints=new LifecycleFootprintReport();
  $beforeCounts=LifecycleFootprintSnapshot::read($reader);
  $settings=AcceptanceTestSettings::build($cfg,$fixture,$_ENV+getenv());
  $keys=LifecycleMutationKeys::generate();$create=$fixture['create'];$create['idempotency_key']=$keys['create'];
  $e=new LifecycleEvidence();
  $created=(new \Titan\BookingSidecar\BookingWriteCoordinator($db,$settings))->create($create);
  $id=LifecycleBookingResult::created($created);
  $afterCreate=LifecycleFootprintEvidence::create($reader,$beforeCounts);$footprints->mark('create');
  $committed=LifecycleCommittedReader::read($reader,$id);
  LifecycleCreateEvidence::verify($committed,$id,$fixture);
  LifecycleOrderIdentity::assert($created,$committed);
  $full=LifecycleReadbackAudit::verify($reader,$id,$fixture);$e->mark('real_insert');
  $beforeFull=LifecycleFullBookingReader::read($reader,$id);
  $replay=(new \Titan\BookingSidecar\BookingWriteCoordinator($db,$settings))->create($create);
  LifecycleBookingResult::replay($replay,$id);
  LifecycleReplayEvidence::verify($committed,LifecycleCommittedReader::read($reader,$id));
  LifecycleReplayOrderEvidence::verify($beforeFull,LifecycleFullBookingReader::read($reader,$id));
  LifecycleFootprintEvidence::unchanged($reader,$afterCreate);$footprints->mark('replay');$e->mark('idempotency');
  $conflict=$create;$conflict['idempotency_key']=$keys['conflict'];
  LifecycleConflictAssertion::check(fn()=>(new \Titan\BookingSidecar\BookingWriteCoordinator($db,$settings))->create($conflict));
  LifecycleReplayEvidence::verify($committed,LifecycleCommittedReader::read($reader,$id));
  LifecycleFootprintEvidence::unchanged($reader,$afterCreate);$footprints->mark('conflict');$e->mark('sequential_overlap');
  $moveBaseline=LifecycleAcceptanceBaseline::capture($reader,$id);
  $move=$fixture['reschedule'];$move['booking_id']=$id;$move['idempotency_key']=$keys['reschedule'];
  $moved=(new \Titan\BookingSidecar\BookingRescheduleCoordinator($db,$settings))->reschedule($move);
  LifecycleResultIdentity::assert($moved,$id,'reschedule');
  $rescheduled=LifecycleCommittedReader::read($reader,$id);
  LifecycleRescheduleEvidence::verify($committed,$rescheduled,$id,$move);
  $movedFull=LifecycleMutationAudit::reschedule($reader,$id,$moveBaseline['booking'],$move,$moveBaseline['siblings'],$moveBaseline['order']);
  $moveAudit=LifecycleAuditReport::successful($moveBaseline['booking'],$movedFull,$moveBaseline['siblings']);
  LifecycleFootprintEvidence::unchanged($reader,$afterCreate);$footprints->mark('reschedule');$e->mark('reschedule');
  $cancelBaseline=LifecycleAcceptanceBaseline::capture($reader,$id);
  $cancel=['booking_id'=>$id,'idempotency_key'=>$keys['cancel'],'reason'=>'disposable acceptance lifecycle'];
  $cancelled=(new \Titan\BookingSidecar\BookingCancellationCoordinator($db,$settings))->cancel($cancel,\Titan\BookingSidecar\BookingOwnerIdentity::fromTenantConfig($settings));
  LifecycleResultIdentity::assert($cancelled,$id,'cancellation');
  $final=LifecycleCommittedReader::read($reader,$id);LifecycleCancellationStatus::verify($rescheduled,$final);
  $cancelFull=LifecycleMutationAudit::cancel($reader,$id,$cancelBaseline['booking'],$cancelBaseline['siblings'],$cancelBaseline['order']);
  $cancelAudit=LifecycleAuditReport::successful($cancelBaseline['booking'],$cancelFull,$cancelBaseline['siblings']);
  LifecycleFootprintEvidence::unchanged($reader,$afterCreate);$footprints->mark('cancel');$e->mark('cancel');
  LifecycleMutationOrder::assert($e->export());
  return LifecycleReport::from($e,['booking'=>LifecycleEvidenceSnapshot::from($final),'committed_associations'=>$full,'reschedule_audit'=>$moveAudit,'cancellation_audit'=>$cancelAudit,'footprint_stages'=>$footprints->export()]);
 }
}
