<?php
namespace App\Services\Tenants;
/** Domain ownership proof via DNS TXT; no app-origin HTTP self-request/SSRF. */
final class DomainVerification {
 public static function txtName(string $hostname):string{return '_titan-verify.'.DomainHostname::normalize($hostname);}
 public static function matches(string $hostname,string $token,array $records):bool {
  foreach($records as $record){$candidate=(string)($record['txt']??'');if(hash_equals('titan-site='.$token,$candidate))return true;}
  return false;
 }
 public static function verify(string $hostname,string $token):bool {
  $records=@dns_get_record(self::txtName($hostname),DNS_TXT);return is_array($records)&&self::matches($hostname,$token,$records);
 }
}
