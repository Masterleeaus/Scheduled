<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemFingerprint { public static function make(int $owner,string $host,array $selection):string { if($owner<1||$host==='')throw new \InvalidArgumentException('Invalid business');return hash('sha256',json_encode(['owner'=>$owner,'host'=>$host,'selection'=>RedeemSelection::canonical($selection)],JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES)); } }
