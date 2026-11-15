<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitLedgerState { public static function validate(array $row,int $owner,string $fingerprint):string {if((int)($row['owner_id']??0)!==$owner||!hash_equals($fingerprint,(string)($row['fingerprint']??'')))throw new \RuntimeException('Claim ownership mismatch');if(!in_array($row['state']??null,['claimed','committed'],true))throw new \RuntimeException('Invalid claim state');return $row['state'];}}
