<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
final class ConcurrencyProcessOutput {
 public static function append(string $current,string $chunk,int $limit=65536):string {if($limit<1024||$limit>1048576)throw new \InvalidArgumentException('Invalid output limit');if(strlen($current)>$limit||strlen($chunk)>$limit-strlen($current))throw new \RuntimeException('Concurrency worker output exceeded safety limit');return $current.$chunk;}
}
