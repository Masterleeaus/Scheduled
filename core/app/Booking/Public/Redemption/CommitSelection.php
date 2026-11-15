<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitSelection { public static function parse(array $payload):array {return RedeemSelection::canonical($payload);}}
