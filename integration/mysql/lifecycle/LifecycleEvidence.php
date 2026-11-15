<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Evidence is local to an actual test execution; never mark concurrency from sequential tests. */
final class LifecycleEvidence { private array $flags=['real_insert'=>false,'sequential_overlap'=>false,'idempotency'=>false,'cancel'=>false,'reschedule'=>false,'pricing_parity'=>false,'tenant_isolation'=>false,'concurrent_overlap'=>false];public function mark(string $key):void {if(!array_key_exists($key,$this->flags))throw new \InvalidArgumentException('Unknown lifecycle evidence');$this->flags[$key]=true;}public function export():array{return $this->flags;} public function productionCertified():bool{return false;} }
