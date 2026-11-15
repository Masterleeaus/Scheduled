<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class RedeemSelection { public static function canonical(array $selection):array { $keys=['service_id','method_id','unit_id','staff_id','quantity','date_time','duration_minutes','timezone']; if(array_keys($selection)!==$keys) { if(array_diff(array_keys($selection),$keys)||count($selection)!==count($keys))throw new \InvalidArgumentException('Unexpected selection'); } $out=[];foreach($keys as $key){if(!array_key_exists($key,$selection))throw new \InvalidArgumentException('Incomplete selection');$out[$key]=$selection[$key];}return $out;} }
