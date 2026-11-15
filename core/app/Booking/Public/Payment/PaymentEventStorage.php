<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
use Illuminate\Support\Facades\DB;
/** Durable event inbox; provider settlement remains independent and unconfirmed. */
final class PaymentEventStorage {
    public function record(array $event): array {
        $fingerprint=PaymentReceiptFingerprint::make($event);
        return DB::transaction(function()use($event,$fingerprint){
            $now=now();
            // The unique constraint also protects against workers racing on first delivery.
            $inserted=DB::table('booking_payment_events')->insertOrIgnore([
                'provider'=>$event['provider'],'event_id'=>$event['event_id'],
                'payment_id'=>$event['payment_id'],'kind'=>$event['kind'],
                'booking_id'=>$event['booking_id'],'tenant_id'=>$event['tenant_id'],
                'amount_minor'=>$event['amount_minor'],'currency'=>$event['currency'],
                'payload_sha256'=>$event['payload_hash'],'fingerprint'=>$fingerprint,
                'state'=>'received','created_at'=>$now,'updated_at'=>$now,
            ]);
            $row=DB::table('booking_payment_events')->where('provider',$event['provider'])->where('event_id',$event['event_id'])->lockForUpdate()->first();
            if(!$row)throw new \RuntimeException('Payment receipt persistence failed');
            PaymentReceiptFingerprint::replay($row->fingerprint,$fingerprint);
            if(!$inserted)return ['receipt'=>'duplicate','settled'=>false,'booking_confirmed'=>false];
            // No booking write here; readback and payment-provider retrieval are mandatory
            // before any reconciliation worker may promote this event.
            DB::table('booking_payment_events')->where('id',$row->id)->update([
                'state'=>'awaiting_reconciliation','updated_at'=>$now
            ]);
            return ['receipt'=>'recorded','settled'=>false,'booking_confirmed'=>false];
        },3);
    }
}
