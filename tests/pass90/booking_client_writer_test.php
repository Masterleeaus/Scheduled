<?php
declare(strict_types=1);
$root=dirname(__DIR__,2).'/private/booking-sidecar/';
spl_autoload_register(static function(string $class)use($root):void {
    $prefix='Titan\\BookingSidecar\\';
    if(strpos($class,$prefix)===0){$file=$root.substr($class,strlen($prefix)).'.php';if(is_file($file))require_once $file;}
});
class MemoryStatement extends PDOStatement {
    public array $args=[];
    public bool $success=true;
    public function __construct(){}
    public function execute(?array $params=null):bool { $this->args=$params??[];return $this->success; }
    public function rowCount():int{return 1;}
}
class MemoryPDO extends PDO {
    public MemoryStatement $lastStatement;
    public string $lastSql='';
    public function __construct(){}
    public function prepare(string $query,array $options=[]):PDOStatement|false {
        $this->lastSql=$query;$this->lastStatement=new MemoryStatement();return $this->lastStatement;
    }
    public function lastInsertId(?string $name=null):string|false{return '23';}
}
function assertPass(bool $yes,string $reason):void {global $n;if(!$yes)throw new RuntimeException($reason);$n++;}
$n=0;$db=new MemoryPDO();$writer=new Titan\BookingSidecar\BookingLegacyClientWriter($db);
$id=$writer->insert(44,['name'=>'Test User','email'=>'user@example.test','phone'=>'0400000000'],60,['address'=>'21 First St','notes'=>'Access via side gate']);
assertPass($id===23,'Native writer returns inserted id');
assertPass(strpos($db->lastSql,'ct_order_client_info')!==false,'Native Cleanto table');
assertPass(strpos($db->lastSql,'client_personal_info')!==false,'Native personal info column');
assertPass(count($db->lastStatement->args)===6,'Six parameters to prepared SQL');
assertPass($db->lastStatement->args[0]===44,'Order ID preserved');
assertPass($db->lastStatement->args[1]==='Test User','Customer name preserved');
assertPass($db->lastStatement->args[2]==='user@example.test','Customer email preserved');
assertPass($db->lastStatement->args[3]==='0400000000','Customer phone preserved');
$info=unserialize(base64_decode($db->lastStatement->args[4],true),['allowed_classes'=>false]);
assertPass($info['address']==='21 First St','Address parameter written');
assertPass($info['notes']==='Access via side gate','Notes parameter written');
assertPass($db->lastStatement->args[5]===60,'Duration preserved');
$writer->insert(44,['name'=>'Test User','email'=>'user@example.test','phone'=>'0400000000'],60);
$empty=unserialize(base64_decode($db->lastStatement->args[4],true),['allowed_classes'=>false]);
assertPass($empty['address']===''&&$empty['notes']==='','Old callers produce valid Cleanto fields');
try{$writer->insert(44,['name'=>'Test User','email'=>'user@example.test','phone'=>'0400000000'],60,['paid'=>true]);throw new RuntimeException('Accepted privileged field');}catch(InvalidArgumentException $e){$n++;}
echo "PASS $n native insert assertions\n";
