<?php
declare(strict_types=1);
namespace App\Booking\Public\Payment;
/** Aggregate read-only inbox status, never exposes customer identifiers. */
final class PaymentEventMetrics {
    public static function summarize(array $rows): array {
        $allowed=['received','awaiting_reconciliation','reconciled','review_required'];
        $c=array_fill_keys($allowed,0);
        foreach($rows as $row){
            $state=$row['state']??null;
            if(!is_string($state)||!isset($c[$state]))throw new \InvalidArgumentException('Unknown payment inbox state');
            $count=$row['total']??null;
            if(!is_int($count)||$count<0)throw new \InvalidArgumentException('Invalid payment inbox count');
            $c[$state]+=$count;
        }
        return ['counts'=>$c,'outstanding'=>$c['received']+$c['awaiting_reconciliation']+$c['review_required'],'complete'=>$c['reconciled']];
    }
}
