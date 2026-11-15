<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Scope is bound to a disposable local database, never production. */
final class LifecycleTestScope { public static function assert(array $cfg,array $fixture):void {DisposableName::assert((string)$cfg['database']); if(($fixture['database']??null)!==$cfg['database'])throw new \DomainException('Fixture/test database mismatch'); if(($fixture['environment']??null)!=='disposable')throw new \DomainException('Fixture must identify a disposable environment');} }
