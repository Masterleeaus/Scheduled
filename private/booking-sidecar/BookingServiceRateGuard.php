<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Reuses Cleanto rates; does not accept a client-supplied amount. */
final class BookingServiceRateGuard {
 public function __construct(private \PDO $db,private array $verifiedSettings=[]){}
 public function confirmedRate(array $service):string {
  BookingPreviewSettings::assertVerified(BookingPreviewRateAssumptions::settings($this->verifiedSettings));
  $selection=BookingPriceInput::parse($service);
  $rate=(new BookingPriceReader($this->db))->inspect($selection['method_unit_id']);
  $price=BookingPriceAuthority::verify($selection,$rate['unit'],$rate['rules']);
  if(($price['verified_scope']??'')!=='exact_rule_no_addons')throw new \DomainException('Unverified rate');
  return BookingPriceDecimal::display($price['total_cents']);
 }
}
