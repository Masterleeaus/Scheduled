<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;
/** Native Cleanto staff eligibility, restricted to disabled services; schedules are read-only. */
final class CleantoStaffAdministration {
 public function __construct(private \PDO $db,private array $config) {}
 public static function parseIds(string $raw):array {
  if(strlen($raw)>2048)throw new \DomainException('Service assignments too long.');
  if(trim($raw)==='')return [];
  $ids=[];
  foreach(explode(',',$raw) as $part){
   $part=trim($part);
   if(!preg_match('/^[1-9][0-9]{0,9}$/D',$part)||(float)$part>2147483647)throw new \DomainException('Malformed native staff service IDs.');
   $id=(int)$part;if(isset($ids[$id]))throw new \DomainException('Duplicate native staff service IDs.');$ids[$id]=true;
  }
  return array_keys($ids);
 }
 private function ready():void {
  if(($this->config['service_management_enabled']??false)!==true || ($this->config['staff_management_enabled']??false)!==true)throw new \RuntimeException('Native staff administration disabled for tenant.');
  if($this->db->getAttribute(\PDO::ATTR_DRIVER_NAME)!=='mysql'||$this->db->getAttribute(\PDO::ATTR_EMULATE_PREPARES))throw new \RuntimeException('Native verified MySQL connection required.');
  $name=$this->config['database_name']??null;
  if(!is_string($name)||!preg_match('/^[A-Za-z0-9_]{1,64}$/D',$name)||$this->db->query('SELECT DATABASE()')->fetchColumn()!==$name)throw new \RuntimeException('Tenant database mismatch.');
 }
 public static function updateInput(array $payload):array {
  if(array_diff(array_keys($payload),['staff_id','service_id','assigned']))throw new \InvalidArgumentException('Unexpected assignment fields.');
  $staff=$payload['staff_id']??null;$service=$payload['service_id']??null;$assigned=$payload['assigned']??null;
  if(!is_int($staff)||$staff<1||!is_int($service)||$service<1||!is_bool($assigned))throw new \InvalidArgumentException('Strict positive IDs and boolean assignment required.');
  return [$staff,$service,$assigned];
 }
 public static function modifiedIds(array $ids,int $service,bool $assigned):array {
  $ids=array_values(array_filter($ids,static fn($id)=>$id!==$service));
  if($assigned)$ids[]=$service;
  sort($ids,SORT_NUMERIC);
  return $ids;
 }
 /** Strict scheduling payload: update one existing native row, never rebuild weekly calendars. */
 public static function scheduleInput(array $payload):array {
  if(array_diff(array_keys($payload),['staff_id','shift_id','start','end','off']))throw new \InvalidArgumentException('Unexpected schedule fields.');
  $staff=$payload['staff_id']??null;$shift=$payload['shift_id']??null;
  $start=$payload['start']??null;$end=$payload['end']??null;$off=$payload['off']??null;
  if(!is_int($staff)||$staff<1||!is_int($shift)||$shift<1||!is_string($start)||!is_string($end)||!is_bool($off))throw new \InvalidArgumentException('Malformed schedule payload.');
  foreach([$start,$end] as $time){
   if(!preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]:00$/D',$time))throw new \InvalidArgumentException('Schedule times require HH:MM:00.');
  }
  if($start>=$end)throw new \InvalidArgumentException('Shift end must be later than start; overnight shifts require native Cleanto administration.');
  if(((int)substr($start,0,2)*60+(int)substr($start,3,2))%5!==0||((int)substr($end,0,2)*60+(int)substr($end,3,2))%5!==0)throw new \InvalidArgumentException('Shift times require five-minute increments.');
  return [$staff,$shift,$start,$end,$off];
 }
 /** Schedule mutation disabled independently from staff eligibility. */
 private function updateShift(array $payload):array {
  if(($this->config['schedule_management_enabled']??false)!==true)throw new \RuntimeException('Native schedule writes disabled for tenant.');
  [$staffId,$shiftId,$start,$end,$off]=self::scheduleInput($payload);
  $this->db->beginTransaction();
  try {
   $staff=$this->db->prepare("SELECT status FROM ct_users WHERE id=? AND usertype='staff' FOR UPDATE");$staff->execute([$staffId]);
   if($staff->fetchColumn()!=='D')throw new \RuntimeException('Schedule editing requires a disabled staff account.');
   $q=$this->db->prepare('SELECT id,week_id,weekday_id,provider_schedule_type FROM ct_week_days_available WHERE id=? AND provider_id=? FOR UPDATE');$q->execute([$shiftId,$staffId]);$shift=$q->fetch(\PDO::FETCH_ASSOC);
   if(!$shift||($shift['provider_schedule_type']??null)!=='weekly'||(int)$shift['week_id']!==1||(int)$shift['weekday_id']<1||(int)$shift['weekday_id']>7)throw new \RuntimeException('Only existing weekly shift rows are editable.');
   // Conservative history guard: do not change availability for staff ever referenced in bookings.
   $history=$this->db->prepare("SELECT id FROM ct_bookings WHERE FIND_IN_SET(?,REPLACE(staff_ids,' ',''))>0 LIMIT 1");$history->execute([(string)$staffId]);
   if($history->fetchColumn()!==false)throw new \RuntimeException('Staff booking history prevents schedule edits.');
   foreach(['ct_schedule_breaks','ct_schedule_offtimes'] as $table){
    $check=$this->db->prepare('SELECT provider_id FROM '.$table.' WHERE provider_id=? LIMIT 1');$check->execute([$staffId]);
    if($check->fetchColumn()!==false)throw new \RuntimeException('Existing breaks or time off require native Cleanto schedule management.');
   }
   $update=$this->db->prepare('UPDATE ct_week_days_available SET day_start_time=?,day_end_time=?,off_day=? WHERE id=? AND provider_id=?');
   $update->execute([$start,$end,$off?'Y':'N',$shiftId,$staffId]);
   $read=$this->db->prepare('SELECT day_start_time,day_end_time,off_day FROM ct_week_days_available WHERE id=? AND provider_id=?');$read->execute([$shiftId,$staffId]);$row=$read->fetch(\PDO::FETCH_ASSOC);
   if(!$row||$row['day_start_time']!==$start||$row['day_end_time']!==$end||$row['off_day']!==($off?'Y':'N'))throw new \RuntimeException('Native schedule readback mismatch.');
   $this->db->commit();
   return ['staff_id'=>$staffId,'shift_id'=>$shiftId,'start'=>$start,'end'=>$end,'off'=>$off,'verified'=>true];
  }catch(\Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
 }
 public function execute(string $operation,array $payload):array {
  $this->ready();
  if($operation==='owner_staff_schedule_update')return $this->updateShift($payload);
  if($operation==='owner_staff_overview'){
   if($payload!==[])throw new \InvalidArgumentException('Staff overview accepts no parameters.');
   $users=$this->db->query("SELECT id,first_name,last_name,status,enable_booking,service_ids FROM ct_users WHERE usertype='staff' ORDER BY id LIMIT 201")->fetchAll(\PDO::FETCH_ASSOC);
   if(count($users)>200)throw new \RuntimeException('Too many staff accounts for this screen.');
   $result=[];
   foreach($users as $user){
    $staffId=(int)$user['id'];
    $shifts=(new ScheduleReader($this->db))->shifts($staffId);
    if(count($shifts)>100)throw new \RuntimeException('Unexpectedly large staff schedule.');
    $result[]=['id'=>$staffId,'name'=>trim((string)$user['first_name'].' '.(string)$user['last_name']),'status'=>(string)$user['status'],'booking_enabled'=>(string)($user['enable_booking']??''),'service_ids'=>self::parseIds((string)($user['service_ids']??'')),'shifts'=>array_map(static fn($r)=>['id'=>(int)$r['id'],'type'=>(string)$r['provider_schedule_type'],'week'=>(int)$r['week_id'],'weekday'=>(int)$r['weekday_id'],'start'=>(string)$r['day_start_time'],'end'=>(string)$r['day_end_time'],'off'=>(string)$r['off_day']],$shifts)];
   }
   return ['staff'=>$result,'schedule_editable'=>(($this->config['schedule_management_enabled']??false)===true),'assignment_scope'=>'disabled_services_only'];
  }
  if($operation!=='owner_staff_service_assignment')throw new \InvalidArgumentException('Unsupported staff operation.');
  [$staffId,$serviceId,$assigned]=self::updateInput($payload);
  $this->db->beginTransaction();
  try {
   // Lock parent service first; the update is intentionally restricted to disabled services.
   $s=$this->db->prepare('SELECT status FROM ct_services WHERE id=:id FOR UPDATE');$s->execute(['id'=>$serviceId]);$status=$s->fetchColumn();
   if($status!=='D')throw new \RuntimeException('Service must exist and be disabled to change staff eligibility.');
   $q=$this->db->prepare("SELECT id,service_ids FROM ct_users WHERE id=:id AND usertype='staff' AND status='E' FOR UPDATE");$q->execute(['id'=>$staffId]);$user=$q->fetch(\PDO::FETCH_ASSOC);
   if(!$user)throw new \RuntimeException('Active staff member not found.');
   $before=self::parseIds((string)($user['service_ids']??''));$after=self::modifiedIds($before,$serviceId,$assigned);$csv=implode(',',$after);
   if(strlen($csv)>2048)throw new \RuntimeException('Staff service assignments exceed supported size.');
   $u=$this->db->prepare('UPDATE ct_users SET service_ids=:ids WHERE id=:id');$u->execute(['ids'=>$csv,'id'=>$staffId]);
   $check=$this->db->prepare('SELECT service_ids FROM ct_users WHERE id=:id');$check->execute(['id'=>$staffId]);
   if(self::parseIds((string)$check->fetchColumn())!==$after)throw new \RuntimeException('Staff assignment readback mismatch.');
   $this->db->commit();
   return ['staff_id'=>$staffId,'service_id'=>$serviceId,'assigned'=>$assigned,'service_ids'=>$after,'schedule_changed'=>false];
  }catch(\Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
 }
}
