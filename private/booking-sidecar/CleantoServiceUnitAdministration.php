<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;

/** Native, tenant-scoped Cleanto unit administration. Never changes active services. */
final class CleantoServiceUnitAdministration {
 public function __construct(private \PDO $db, private array $tenantConfig) {}
 public static function input(array $payload):array {
  if(array_diff(array_keys($payload),['unit_id','base_price','duration_minutes']))throw new \InvalidArgumentException('Unexpected unit fields.');
  $id=$payload['unit_id']??null;$duration=$payload['duration_minutes']??null;
  if(!is_int($id)||$id<1||!is_int($duration)||$duration<5||$duration>1440||$duration%5!==0)throw new \InvalidArgumentException('Valid unit ID and five-minute duration increments (5–1440) required.');
  $cents=BookingPriceDecimal::cents($payload['base_price']??null);
  if($cents>100000000)throw new \InvalidArgumentException('Base price exceeds supported maximum.');
  return ['unit_id'=>$id,'base_price'=>BookingPriceDecimal::display($cents),'duration_minutes'=>$duration];
 }
 private function assertConnection():void {
  if(($this->tenantConfig['service_management_enabled']??false)!==true || ($this->tenantConfig['unit_management_enabled']??false)!==true)throw new \RuntimeException('Unit administration is disabled for this tenant.');
  if($this->db->getAttribute(\PDO::ATTR_DRIVER_NAME)!=='mysql'||$this->db->getAttribute(\PDO::ATTR_EMULATE_PREPARES))throw new \RuntimeException('Verified MySQL connection required.');
  $expected=$this->tenantConfig['database_name']??null;
  if(!is_string($expected)||!preg_match('/^[A-Za-z0-9_]{1,64}$/D',$expected)||$this->db->query('SELECT DATABASE()')->fetchColumn()!==$expected)throw new \RuntimeException('Tenant database identity mismatch.');
 }
 public function execute(string $operation,array $payload):array {
  $this->assertConnection();
  if($operation==='owner_service_units'){
   if($payload!==[])throw new \InvalidArgumentException('Unit listing accepts no payload.');
   $s=$this->db->query("SELECT u.id,u.services_id AS service_id,u.methods_id AS method_id,u.units_title AS title,u.base_price,u.uduration AS duration_minutes,u.status,s.status AS service_status,m.method_title AS method_name FROM ct_service_methods_units u INNER JOIN ct_services s ON s.id=u.services_id INNER JOIN ct_services_method m ON m.id=u.methods_id AND m.service_id=u.services_id ORDER BY u.id DESC LIMIT 501");
   $rows=$s->fetchAll(\PDO::FETCH_ASSOC);if(count($rows)>500)throw new \RuntimeException('Too many pricing units: use Cleanto native administration.');
   return ['units'=>array_map(static fn($r)=>['id'=>(int)$r['id'],'service_id'=>(int)$r['service_id'],'method_id'=>(int)$r['method_id'],'title'=>(string)$r['title'],'method_name'=>(string)$r['method_name'],'base_price'=>(string)$r['base_price'],'duration_minutes'=>(int)$r['duration_minutes'],'status'=>(string)$r['status'],'service_status'=>(string)$r['service_status'],'editable'=>$r['service_status']==='D'],$rows)];
  }
  if($operation!=='owner_service_unit_update')throw new \InvalidArgumentException('Unsupported unit operation.');
  $input=self::input($payload);
  $this->db->beginTransaction();
  try {
   $s=$this->db->prepare('SELECT u.id,u.services_id,u.methods_id,s.status AS service_status FROM ct_service_methods_units u INNER JOIN ct_services s ON s.id=u.services_id WHERE u.id=:id FOR UPDATE');
   $s->execute(['id'=>$input['unit_id']]);$row=$s->fetch(\PDO::FETCH_ASSOC);
   if(!$row)throw new \RuntimeException('Unit not found.');
   // Serialise with service activation by also locking its parent service row.
   $parent=$this->db->prepare('SELECT status FROM ct_services WHERE id=:id FOR UPDATE');$parent->execute(['id'=>(int)$row['services_id']]);
   if($parent->fetchColumn()!=='D')throw new \RuntimeException('Only units belonging to disabled services can be edited.');
   // Additional check prevents edits to units referenced by appointments even if service disabled.
   $booking=$this->db->prepare('SELECT id FROM ct_bookings WHERE method_unit_id=:id LIMIT 1');$booking->execute(['id'=>$input['unit_id']]);
   if($booking->fetchColumn()!==false)throw new \RuntimeException('Unit has booking history and must be managed through native Cleanto administration.');
   $up=$this->db->prepare('UPDATE ct_service_methods_units SET base_price=:price,uduration=:duration WHERE id=:id');
   $up->execute(['price'=>$input['base_price'],'duration'=>$input['duration_minutes'],'id'=>$input['unit_id']]);
   $read=$this->db->prepare('SELECT base_price,uduration FROM ct_service_methods_units WHERE id=:id');$read->execute(['id'=>$input['unit_id']]);$saved=$read->fetch(\PDO::FETCH_ASSOC);
   if(!$saved||BookingPriceDecimal::cents((string)$saved['base_price'])!==BookingPriceDecimal::cents($input['base_price'])||(int)$saved['uduration']!==$input['duration_minutes'])throw new \RuntimeException('Pricing unit readback did not match.');
   $this->db->commit();
   return ['unit_id'=>$input['unit_id'],'base_price'=>$input['base_price'],'duration_minutes'=>$input['duration_minutes'],'service_status'=>'D','configured_for_booking'=>false];
  } catch(\Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
 }
}
