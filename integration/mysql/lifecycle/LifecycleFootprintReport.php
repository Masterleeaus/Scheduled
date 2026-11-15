<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Record only stage names, never customer details or database counts. */
final class LifecycleFootprintReport {
 private array $verified=[];
 public function mark(string $stage):void {
  if(!in_array($stage,['create','replay','conflict','reschedule','cancel'],true)||isset($this->verified[$stage]))throw new \LogicException('Invalid footprint stage');
  $this->verified[$stage]=true;
 }
 public function export():array {
  foreach(['create','replay','conflict','reschedule','cancel'] as $stage)if(!isset($this->verified[$stage]))throw new \LogicException('Missing footprint evidence: '.$stage);
  return array_keys($this->verified);
 }
}
