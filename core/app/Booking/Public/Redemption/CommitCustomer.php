<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitCustomer { public static function parse(array $payload):array { $p=RedeemPayload::validate($payload);return ['customer_email'=>$p['customer_email'],'customer_name'=>$p['customer_name']];}}
