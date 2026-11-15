<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;

/** Tenant-local Cleanto service metadata writes. Disabled until separately approved per instance.
 * No price, staff, schedule, payment or booking mutation is performed here.
 */
final class CleantoServiceAdministration {
 public function __construct(private \PDO $db, private array $tenantConfig) {}
 public static function input(array $payload,bool $creating):array {
  $title=$payload['title']??null;
  $description=$payload['description']??'';
  if(!is_string($title)||trim($title)===''||strlen(trim($title))>120)throw new \InvalidArgumentException('Service name must be 1–120 characters.');
  if(!is_string($description)||strlen($description)>2000)throw new \InvalidArgumentException('Service description exceeds 2000 characters.');
  $id=null;
  if(!$creating){$raw=$payload['id']??null;if(!is_int($raw)||$raw<1)throw new \InvalidArgumentException('A positive service ID is required.');$id=$raw;}
  if(array_diff(array_keys($payload),$creating?['title','description']:['id','title','description']))throw new \InvalidArgumentException('Unexpected service fields.');
  return ['id'=>$id,'title'=>trim($title),'description'=>trim($description)];
 }
 public function execute(string $operation,array $payload):array {
  if(($this->tenantConfig['service_management_enabled']??false)!==true)throw new \RuntimeException('Service administration is disabled for this tenant.');
  if($this->db->getAttribute(\PDO::ATTR_DRIVER_NAME)!=='mysql'||$this->db->getAttribute(\PDO::ATTR_EMULATE_PREPARES))throw new \RuntimeException('Verified MySQL connection required.');
  $expected=$this->tenantConfig['database_name']??null;
  if(!is_string($expected)||!preg_match('/^[A-Za-z0-9_]{1,64}$/D',$expected))throw new \RuntimeException('Tenant database identity is not configured.');
  $actual=$this->db->query('SELECT DATABASE()')->fetchColumn();
  if($actual!==$expected)throw new \RuntimeException('Tenant database identity mismatch.');
  if($operation==='owner_service_list'){
   $rows=$this->db->query('SELECT id,title,description,status FROM ct_services ORDER BY id DESC LIMIT 251')->fetchAll(\PDO::FETCH_ASSOC);
   return ['services'=>array_map(static fn($row)=>['id'=>(int)$row['id'],'title'=>(string)$row['title'],'description'=>(string)$row['description'],'status'=>(string)$row['status']],$rows)];
  }
  $creating=$operation==='owner_service_create';
  if(!$creating&&$operation!=='owner_service_update')throw new \InvalidArgumentException('Unsupported service operation.');
  $input=self::input($payload,$creating);
  $this->db->beginTransaction();
  try {
   if($creating){
    // Original Cleanto ct_services schema: id,title,description,color,image,status,position.
    // Create DISABLED until the owner configures price, units, staff and schedule.
    $query=$this->db->prepare("INSERT INTO ct_services (title,description,color,image,status,position) VALUES (:title,:description,:color,:image,'D',:position)");
    $position=(int)$this->db->query('SELECT COALESCE(MAX(position),0)+1 FROM ct_services')->fetchColumn();
    $query->execute(['title'=>$input['title'],'description'=>$input['description'],'color'=>'#264b70','image'=>'','position'=>$position]);
    $id=(int)$this->db->lastInsertId();if($id<1)throw new \RuntimeException('Service insertion was not confirmed.');
   }else{
    $id=$input['id'];
    $lock=$this->db->prepare('SELECT id FROM ct_services WHERE id=:id FOR UPDATE');$lock->execute(['id'=>$id]);
    if($lock->fetchColumn()===false)throw new \RuntimeException('Service no longer exists.');
    $query=$this->db->prepare('UPDATE ct_services SET title=:title,description=:description WHERE id=:id');
    $query->execute(['title'=>$input['title'],'description'=>$input['description'],'id'=>$id]);
   }
   $read=$this->db->prepare('SELECT id,title,description,status FROM ct_services WHERE id=:id');$read->execute(['id'=>$id]);
   $row=$read->fetch(\PDO::FETCH_ASSOC);
   if(!$row||(int)$row['id']!==$id||$row['title']!==$input['title']||$row['description']!==$input['description'])throw new \RuntimeException('Service readback mismatch.');
   $this->db->commit();
   return ['id'=>$id,'title'=>$row['title'],'status'=>$row['status'],'configured_for_booking'=>false];
  }catch(\Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
 }
}
