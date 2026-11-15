<?php
declare(strict_types=1);
namespace App\Booking\Public\Redemption;
final class CommitIntentScope { public static function assert(array $intent):void {if(($intent['scope']??null)!=='booking_commit_v1')throw new \RuntimeException('Preview-only intent cannot create a booking');if(($intent['v']??null)!==2)throw new \RuntimeException('Unsupported commit intent');}}
