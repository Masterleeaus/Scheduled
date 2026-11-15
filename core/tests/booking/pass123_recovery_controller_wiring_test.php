<?php
declare(strict_types=1);
/** Prevent regression of the customer identity handoff in the real HTTP controller. */
$source=file_get_contents(__DIR__.'/../../app/Http/Controllers/Front/BookingPublicRecoveryController.php');
$reader=file_get_contents(__DIR__.'/../../app/Booking/Public/Checkout/CheckoutRecoveryReader.php');
if(!is_string($source)||!is_string($reader))throw new RuntimeException('Missing source');
$checks=0;
$assert=static function(bool $value,string $message)use(&$checks):void {
 if(!$value)throw new RuntimeException($message);$checks++;
};
$assert((bool)preg_match('/CheckoutRecovery::lookup\s*\(/',$source),'Controller must invoke recovery');
$assert((bool)preg_match('/static\s+fn\s*\(\s*\$key\s*,\s*\$tenant\s*,\s*\$hash\s*,\s*\$selection\s*,\s*\$identity\s*,\s*\$personalInfo\s*\)\s*=>\s*CheckoutRecoveryReader::read\s*\(\s*\$pdo\s*,\s*\$key\s*,\s*\$tenant\s*,\s*\$hash\s*,\s*\$selection\s*,\s*\$identity\s*,\s*\$personalInfo\s*\)/',$source),'Controller must pass validated customer identity into read-only reader');
$assert(str_contains($reader,'array $expectedIdentity,array $expectedPersonalInfo):?array'),'Reader must require identity, with no empty default');
$assert(str_contains($reader,'FROM ct_order_client_info'),'Reader must use native order table');
$assert(str_contains($reader,'order_date,client_id'),'Booking readback must include persisted order date');
$assert(!str_contains($reader,'if ($expectedIdentity !== [])'),'Identity check cannot be bypassed by empty values');
echo "Pass 123 controller and recovery reader wiring: {$checks} assertions passed\n";
