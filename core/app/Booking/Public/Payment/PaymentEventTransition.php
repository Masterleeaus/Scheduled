<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Ordered observation only. Webhooks cannot directly confirm bookings or revenue. */
final class PaymentEventTransition {
    public static function observe(string $current,string $eventKind): string {
        if(!in_array($current,['unseen','pending','succeeded_observed','failed_observed','refunded_observed','review_required'],true))throw new \InvalidArgumentException('Unknown payment status');
        if(!in_array($eventKind,['succeeded','failed','refunded'],true))throw new \InvalidArgumentException('Unknown payment event');
        if($current==='review_required')return $current;
        if($eventKind==='refunded')return 'refunded_observed';
        if($current==='refunded_observed')return 'review_required';
        if($current==='succeeded_observed' && $eventKind==='failed')return 'succeeded_observed';
        if($current==='failed_observed' && $eventKind==='succeeded')return 'succeeded_observed';
        return $eventKind.'_observed';
    }
}
