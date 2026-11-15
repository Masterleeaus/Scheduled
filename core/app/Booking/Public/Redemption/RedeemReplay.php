<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemReplay { public static function interpret(array $row,int $owner,string $fingerprint):array {if((int)($row['owner_id']??0)!==$owner||($row['fingerprint']??null)!==$fingerprint)throw new \RuntimeException('Claim does not match intent');$state=$row['state']??null;if($state==='committed'){ $id=filter_var($row['booking_id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);if($id===false)throw new \RuntimeException('Corrupt committed claim');return ['replay'=>true,'booking_id'=>(int)$id];}if($state==='claimed')return ['replay'=>false,'pending'=>true];throw new \RuntimeException('Unsupported claim state');} }
