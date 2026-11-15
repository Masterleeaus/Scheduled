<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
final class CatalogueIdentifier {public static function parse($raw):int {if(!is_int($raw)&&!(is_string($raw)&&preg_match('/^[1-9][0-9]{0,9}$/D',$raw)))throw new \InvalidArgumentException('Invalid catalogue identifier');$n=(int)$raw;if($n<1||$n>2147483647)throw new \InvalidArgumentException('Invalid catalogue identifier');return $n;}}
