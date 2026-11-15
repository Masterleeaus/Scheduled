<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitSameDatabase { public static function assert(\PDO $writer,\PDO $claims):void {if($writer!==$claims)throw new \RuntimeException('Booking writer and claim must share the same PDO transaction');}}
