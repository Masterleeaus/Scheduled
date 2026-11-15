<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Verify session and transactional storage before calling any coordinator. */
final class LifecycleDatabaseReadiness { public static function assert(\PDO $db,string $expected):void {IsolationSentinel::assert($db,$expected); SessionSafetyProbe::assert($db);$engines=TransactionalTableAudit::read($db);$bad=SchemaFailureReport::fromInventories(SchemaInventoryReader::read($db),$engines);if(($bad['valid']??false)!==true)throw new \DomainException('Cleanto test schema not ready: '.implode(', ',array_keys($bad)));} }
