<?php
declare(strict_types=1);
$root = dirname(__DIR__, 2);
spl_autoload_register(static function (string $name) use ($root): void {
    if (strpos($name, 'App\\') !== 0) return;
    $path = $root.'/app/'.str_replace('\\', '/', substr($name, 4)).'.php';
    if (is_file($path)) require_once $path;
});
use App\Booking\Public\Checkout\CheckoutDeploymentProfile;
use App\Booking\Public\Checkout\CheckoutNativeCustomer;
use App\Booking\Public\Checkout\NativeCheckoutSubmissionExecutor;
function check(bool $condition, string $message): void {if (!$condition) throw new RuntimeException($message);}
function denied(callable $f, string $label): void {try {$f();} catch (\Throwable $e) {return;} throw new RuntimeException('Expected refusal: '.$label);}
$goodTenant = [
 'owner_id'=>7, 'booking_writes_enabled'=>true, 'booking_schema_verified'=>true,
 'booking_rate_mapping_verified'=>true, 'booking_concurrency_verified'=>true,
 'verified_pricing_settings'=>[],
];
$evidence = [
 'public_writes_enabled'=>true, 'mysql_lifecycle_passed'=>true,
 'concurrency_passed'=>true, 'pricing_parity_passed'=>true,
 'tenant_isolation_passed'=>true, 'schema_passed'=>true,
 'same_database_transaction'=>true, 'independent_persisted_readback'=>true,
 'staff_locks_held_until_commit'=>true,
];
$profiles = [7 => ['connection'=>'cleanto_test_7', 'tenant'=>$goodTenant,'release_evidence'=>$evidence]];
check(CheckoutDeploymentProfile::tenant(7,$profiles)['connection']==='cleanto_test_7','Trusted connection mapping');
denied(fn()=>CheckoutDeploymentProfile::tenant(8,$profiles),'unknown tenant');
denied(fn()=>CheckoutDeploymentProfile::tenant(0,$profiles),'zero tenant');
$other=$profiles;$other[7]['tenant']['owner_id']=8;
denied(fn()=>CheckoutDeploymentProfile::tenant(7,$other),'owner mismatch');
$other=$profiles;$other[7]['connection']='mysql;other';
denied(fn()=>CheckoutDeploymentProfile::tenant(7,$other),'unsafe connection');
$other=$profiles;unset($other[7]['release_evidence']);
denied(fn()=>CheckoutDeploymentProfile::tenant(7,$other),'missing independent evidence');
$other=$profiles;$other[7]['release_evidence']['concurrency_passed']=false;
denied(fn()=>CheckoutDeploymentProfile::tenant(7,$other),'failed concurrency proof');
$other=$profiles;$other[7]['tenant']['booking_schema_verified']=false;
denied(fn()=>CheckoutDeploymentProfile::tenant(7,$other),'unverified schema');
$other=$profiles;$other[7]['tenant']['verified_pricing_settings']='bad';
denied(fn()=>CheckoutDeploymentProfile::tenant(7,$other),'invalid pricing settings');
$native = CheckoutNativeCustomer::map([
 'first_name'=>'Ada','last_name'=>'Test','email'=>'ada@example.test','phone'=>'0412345678',
 'address'=>'10 Test Street','notes'=>'Use side entrance','consent'=>true
]);
check($native['identity']['name']==='Ada Test','Mapped name');
check($native['personal_info']['address']==='10 Test Street','Private autoload and address');
check($native['personal_info']['notes']==='Use side entrance','Mapped notes');
$executor = new NativeCheckoutSubmissionExecutor([], dirname(__DIR__, 3).'/private/booking-sidecar');
denied(fn()=> $executor->commit(7, [], [], [], str_repeat('a',32)), 'no trusted profile before DB access');
$provider=file_get_contents($root.'/app/Providers/AppServiceProvider.php');
check(strpos($provider,'public_commit_enabled')!==false && strpos($provider,'CheckoutSubmissionExecutor::class')!==false,'Server-owned conditional binding');
$config=file_get_contents($root.'/config/booking_internal.php');
check(strpos($config,"'public_commit_tenants' => []")!==false,'Fail-closed default config');
$gateway=file_get_contents($root.'/app/Booking/Public/Redemption/CommitCleantoNativeGateway.php');
check(strpos($gateway,'CommitCleantoLoader::register($privateRoot);') < strpos($gateway,'BookingPersonalInfo::parse($personalInfo)'),'Gateway loads native sidecar before use');
echo "Pass 94: 16 security and native-loader assertions passed; no live booking claim\n";
