<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Normalizes opaque request IDs and creates stable operation-scoped identifiers. */
final class BookingMutationRequestScope {
 public static function key(string $operation,string $requestId):string {
  if(!in_array($operation,['create','cancel','reschedule'],true))throw new \InvalidArgumentException('Unsupported booking mutation');
  if(strlen($requestId)<8||strlen($requestId)>128||!preg_match('/^[A-Za-z0-9_.:-]+$/D',$requestId))throw new \InvalidArgumentException('Invalid request ID');
  return $operation.':'.hash('sha256',$requestId);
 }
}
