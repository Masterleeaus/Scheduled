<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout {
    final class CheckoutNativeCustomer {
        public static function map(array $customer): array {
            return ['identity'=>['name'=>'Test Customer'],'personal_info'=>['address'=>'Test Street']];
        }
    }
    final class CheckoutCommitCompatibility {
        public static function assert(array $selection,array $customer,string $nonce): void {}
    }
    require_once __DIR__.'/../../app/Booking/Public/Checkout/CheckoutSubmissionExecutor.php';
    require_once __DIR__.'/../../app/Booking/Public/Checkout/CheckoutSubmissionGate.php';
    final class Pass98Executor implements CheckoutSubmissionExecutor {
        public array $result=[];
        public function commit(int $owner,array $selection,array $identity,array $personalInfo,string $intentNonce):array { return $this->result; }
    }
}
namespace {
    use App\Booking\Public\Checkout\CheckoutSubmissionGate;
    use App\Booking\Public\Checkout\Pass98Executor;
    $n=0;
    function check98(bool $value,string $message): void {global $n; ++$n; if (!$value) throw new \RuntimeException($message);}
    function reject98(callable $cb): void {try {$cb();}catch(\RuntimeException $e){return;}throw new \RuntimeException('Expected failure');}
    $executor=new Pass98Executor;
    $nonce=str_repeat('a',32);
    $executor->result=['confirmed'=>true,'payment_settled'=>false,'booking_id'=>101,'replay'=>false];
    $fresh=CheckoutSubmissionGate::dispatch(true,$executor,1,[],[],$nonce);
    check98($fresh['confirmed']===true,'fresh confirmed');
    check98($fresh['replay']===false,'fresh is not replay');
    check98($fresh['booking_reference']==='101','fresh reference');
    $executor->result['replay']=true;
    $replay=CheckoutSubmissionGate::dispatch(true,$executor,1,[],[],$nonce);
    check98($replay['replay']===true,'replayed booking preserved');
    check98($replay['booking_reference']===$fresh['booking_reference'],'stable reference');
    foreach ([null,0,1,'false','true'] as $invalid){
        $executor->result['replay']=$invalid;
        reject98(fn()=>CheckoutSubmissionGate::dispatch(true,$executor,1,[],[],$nonce));
        check98(true,'invalid replay flag blocked');
    }
    $controller=file_get_contents(__DIR__.'/../../app/Http/Controllers/Front/BookingPublicSubmissionController.php');
    check98(strpos($controller,"$"."result['replay'] === true ? 200 : 201")!==false,'HTTP replay status correct');
    $native=file_get_contents(__DIR__.'/../../app/Booking/Public/Checkout/NativeCheckoutSubmissionExecutor.php');
    check98(strpos($native,"is_bool($"."result['replay'] ?? null)")!==false,'native executor enforces boolean');
    echo "Pass 98: {$n} replay propagation assertions passed; database not exercised\n";
}
