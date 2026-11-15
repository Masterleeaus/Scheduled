<?php
declare(strict_types=1);
/** Pass 124: inspect actual Cleanto INSERT parameters with a PDO test double. */
$root=dirname(__DIR__,2);
foreach (['BookingPositiveId','BookingAffectedRows','BookingCustomer','BookingLiveWriteCustomer','BookingPersonalInfo','BookingLiveWriteIds','BookingLegacyClientWriter'] as $name) {
 require_once $root.'/private/booking-sidecar/'.$name.'.php';
}
use Titan\BookingSidecar\BookingLegacyClientWriter;
final class Pass124Statement extends PDOStatement {
 public array $parameters=[];
 public function __construct() {}
 public function execute(?array $params=null):bool {$this->parameters=$params??[];return true;}
 public function rowCount():int {return 1;}
}
final class Pass124Pdo extends PDO {
 public ?Pass124Statement $statement=null;
 public string $sql='';
 public function __construct() {}
 public function prepare(string $query,array $options=[]):PDOStatement|false {$this->sql=$query;return $this->statement=new Pass124Statement();}
 public function lastInsertId(?string $name=null):string|false {return '17';}
}
function check124(bool $condition,string $label):void {global $checks;if(!$condition)throw new RuntimeException($label);$checks++;}
$checks=0;
$input=['zip'=>'3000','address'=>'21 Harbour St','city'=>'Melbourne','state'=>'VIC','notes'=>'Intercom 14'];
$db=new Pass124Pdo();
$id=(new BookingLegacyClientWriter($db))->insert(16,['name'=>'Lena Nguyen','email'=>'lena@example.com','phone'=>'+61412345678'],60,$input);
check124($id===17,'Customer ID');
check124(str_contains($db->sql,'INSERT INTO ct_order_client_info'),'Original Cleanto table');
check124(count($db->statement->parameters)===6,'Parameterized six-field insert');
check124($db->statement->parameters[0]===16,'Order ID');
check124($db->statement->parameters[5]===60,'Duration');
$info=unserialize(base64_decode($db->statement->parameters[4],true),['allowed_classes'=>false]);
check124(is_array($info),'Native serialized personal information');
foreach($input as $field=>$value)check124($info[$field]===$value,'Persisted '.$field);
foreach(['vc_status','p_status','contact_status'] as $field)check124($info[$field]==='','Native '.$field);
check124(count($info)===8,'Exactly eight Cleanto fields');
// Verify all three actual persist call sites send normalized structured data.
foreach (['BookingAtomicPersist.php','BookingWriteCoordinator.php','BookingSharedPersist.php'] as $file) {
 $source=file_get_contents($root.'/private/booking-sidecar/'.$file);
 check124((bool)preg_match('/BookingLegacyClientWriter\s*\([^)]*\)\s*\)\s*->insert\s*\(\s*\$order\s*,\s*\$(?:cmd|command)\[\x27customer\x27\]\s*,\s*\$(?:cmd|command)\[\x27duration_minutes\x27\]\s*,\s*\$(?:cmd|command)\[\x27personal_info\x27\](?:\s*\?\?\s*\[\])?\s*\)/',$source),'Personal info forwarded by '.$file);
}
// The writer must refuse invalid location data instead of quietly dropping it.
try {(new BookingLegacyClientWriter($db))->insert(16,['name'=>'Lena Nguyen','email'=>'lena@example.com','phone'=>'+61412345678'],60,['zip'=>"bad\x00"]);throw new RuntimeException('Control character was accepted');}
catch(InvalidArgumentException $exception) {$checks++;}
echo "Pass124 customer-location persistence: $checks assertions passed\n";
