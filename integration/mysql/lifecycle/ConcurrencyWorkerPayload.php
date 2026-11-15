<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyWorkerPayload {
 public static function build(array $fixture,string $runId,string $worker):array {
  ConcurrencyRunId::assert($runId);if(!in_array($worker,['a','b'],true))throw new \InvalidArgumentException('Worker label invalid');
  $command=$fixture['create'];$command['idempotency_key']='concurrency-'.$runId.'-'.$worker;
  return $command;
 }
}
