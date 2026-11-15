<?php
declare(strict_types=1);
namespace Titan\BookingIntegration;
/** Load explicit fixture without evaluating code or accepting arbitrary includes. */
final class LifecycleFixtureLoader { public static function read(string $path):array {if(!is_file($path)||is_link($path)||filesize($path)>65536)throw new \InvalidArgumentException('Fixture must be a regular JSON file under 64 KB');$raw=file_get_contents($path);if($raw===false)throw new \RuntimeException('Fixture unreadable');$value=json_decode($raw,true,32,JSON_THROW_ON_ERROR);if(!is_array($value)||array_is_list($value))throw new \InvalidArgumentException('Fixture object required');return $value;} }
