<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout;
final class CheckoutRequest {
    public static function parse(array $data):array {
        if(count($data)!==2 || !array_key_exists('intent_token',$data) || !array_key_exists('customer',$data) || !is_string($data['intent_token']) || strlen($data['intent_token'])>4200 || !is_array($data['customer'])) throw new \InvalidArgumentException('Invalid checkout request');
        return ['intent_token'=>$data['intent_token'],'customer'=>CheckoutCustomer::parse($data['customer'])];
    }
}
