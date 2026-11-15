<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Pass 07: conservative Cleanto pricing and operational readiness. */
final class BookingPriceReader {
 public function __construct(private \PDO $db){}
 private static function rows(\PDOStatement $s):array {$rows=$s->fetchAll(\PDO::FETCH_ASSOC);if(!is_array($rows))throw new \RuntimeException('Pricing rule rows unavailable');return $rows;}
 public function inspect(int $unitId):array {
  if($unitId<1)throw new \InvalidArgumentException('Unit required');
  $s=$this->db->prepare('SELECT id,services_id,methods_id,minlimit,maxlimit,base_price,status FROM ct_service_methods_units WHERE id=? LIMIT 2');if($s===false||$s->execute([$unitId])!==true)throw new \RuntimeException('Pricing unit query failed');$rows=$s->fetchAll(\PDO::FETCH_ASSOC);if(!is_array($rows))throw new \RuntimeException('Pricing unit rows unavailable');
  if(count($rows)!==1)throw new \DomainException('Unit not uniquely found');
  $s=$this->db->prepare('SELECT id,units,rules,rates FROM ct_services_methods_units_rate WHERE units_id=? ORDER BY id LIMIT 501');if($s===false||$s->execute([$unitId])!==true)throw new \RuntimeException('Pricing rule query failed');
  return ['unit'=>BookingServicePriceSnapshot::fromRow($rows[0]),'rules'=>BookingRateTable::normalize(BookingPricingRuleLimit::complete(self::rows($s)))];
 }
}
