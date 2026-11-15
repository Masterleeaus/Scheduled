<?php
declare(strict_types=1);
spl_autoload_register(static function(string $name): void {
    foreach (['App\\Booking\\Public\\Redemption\\' => '/core/app/Booking/Public/Redemption/', 'Titan\\BookingSidecar\\' => '/private/booking-sidecar/'] as $prefix => $path) {
        if (str_starts_with($name, $prefix)) {
            $file=dirname(__DIR__,2).$path.substr($name, strlen($prefix)).'.php';
            if (is_file($file)) require_once $file;
            return;
        }
    }
});
use App\Booking\Public\Redemption\CommitLegacyFingerprint;
function same97($a,$b,string $reason): void {if ($a!==$b) throw new RuntimeException($reason);}
function diff97($a,$b,string $reason): void {if ($a===$b) throw new RuntimeException($reason);}
$selection=['service_id'=>1,'method_id'=>2,'unit_id'=>3,'staff_id'=>4,'quantity'=>1,'date_time'=>'2026-12-15 09:00:00','duration_minutes'=>60,'timezone'=>'Australia/Melbourne'];
$customer=['name'=>'Test','email'=>'test@example.com','phone'=>'0400000000'];
$base=CommitLegacyFingerprint::make(5,$selection,$customer);
$sortedSelection=$selection;ksort($sortedSelection,SORT_STRING);$sortedCustomer=$customer;ksort($sortedCustomer,SORT_STRING);
$legacy=hash('sha256',json_encode(['owner'=>5,'selection'=>$sortedSelection,'customer'=>$sortedCustomer],JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES));
same97($base,$legacy,'empty location must preserve existing legacy fingerprint');
same97($base,CommitLegacyFingerprint::make(5,$selection,$customer,['address'=>'','notes'=>'']),'empty location regression');
$a=CommitLegacyFingerprint::make(5,$selection,$customer,['address'=>'1 Main St','notes'=>'Front gate']);
$b=CommitLegacyFingerprint::make(5,$selection,$customer,['address'=>'2 Main St','notes'=>'Front gate']);
$c=CommitLegacyFingerprint::make(5,$selection,$customer,['address'=>'1 Main St','notes'=>'Back gate']);
diff97($a,$b,'address mutation must reject replay');
diff97($a,$c,'notes mutation must reject replay');
diff97($a,$base,'personal info must not match legacy empty-info claim');
same97($a,CommitLegacyFingerprint::make(5,array_reverse($selection,true),array_reverse($customer,true),['notes'=>'Front gate','address'=>'1 Main St']),'field order affects identity');
same97($a,CommitLegacyFingerprint::make(5,$selection,$customer,['address'=>' 1 Main St ','notes'=>'Front gate']),'normalization affects identity');
try {CommitLegacyFingerprint::make(5,$selection,$customer,['tenant_id'=>999]);throw new RuntimeException('invalid authority accepted');}catch(InvalidArgumentException $e){}
try {CommitLegacyFingerprint::make(5,$selection,$customer,['address'=>str_repeat('x',251)]);throw new RuntimeException('oversized location accepted');}catch(InvalidArgumentException $e){}
echo "PASS 10 Pass97 personal-info fingerprint checks\n";
