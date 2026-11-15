<?php
namespace App\Services\Tenants;
/** Hostname normalization independent of request-provided forwarding headers. */
final class DomainHostname {
 public static function normalize(string $value):string {
  $value=strtolower(trim(rtrim($value,'.')));
  if(str_contains($value,'://')||str_contains($value,'/')||str_contains($value,'@')||str_contains($value,':'))throw new \InvalidArgumentException('Hostname only');
  if(strlen($value)>253||strlen($value)<4||filter_var($value,FILTER_VALIDATE_IP))throw new \InvalidArgumentException('Invalid hostname');
  foreach(explode('.',$value) as $label){if(strlen($label)<1||strlen($label)>63||!preg_match('/^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/D',$label))throw new \InvalidArgumentException('Invalid DNS label');}
  if(substr_count($value,'.')<1)throw new \InvalidArgumentException('Qualified domain required');
  return $value;
 }
}
