<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 44: assert that persisted Cleanto records match the transaction input. */
final class BookingWriteProofLink { public static function check(array $booking,array $order):void {if(BookingWriteProofId::check($booking['order_id']??null)!==BookingWriteProofId::check($order['order_id']??null))throw new \DomainException('Order link mismatch');if(BookingWriteProofId::check($booking['client_id']??null)!==BookingWriteProofId::check($order['id']??null))throw new \DomainException('Customer link mismatch');} }
