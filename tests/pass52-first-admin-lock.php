<?php
/** Executable Pass 52 test using an in-memory connection fake: no database required. */
namespace Illuminate\Routing { class Controller {} }
namespace Illuminate\Http {
    class Request {
        public $session;
        public function __construct() { $this->session = new \TestSession(); }
        public function session() { return $this->session; }
        public function validate($rules) { return ['first_name'=>'Test','last_name'=>'Owner','email'=>'owner@example.invalid','password'=>'a-long-test-password']; }
    }
}
namespace Illuminate\Support\Facades {
    class DB {
        public static $db;
        public static function connection() { return self::$db; }
    }
    class Hash { public static function make($s) { return 'hashed:'.$s; } }
}
namespace {
    class TestSession {
        public $values=['installer_schema_imported'=>true];
        public function get($key) { return $this->values[$key] ?? null; }
        public function put($key, $value) { $this->values[$key]=$value; }
        public function regenerate() { $this->values['regenerated']=true; }
    }
    function abort_unless($condition,$status) { if (!$condition) throw new \RuntimeException('abort '.$status); }
    function abort_if($condition,$status) { if ($condition) throw new \RuntimeException('abort '.$status); }
    function storage_path($s) { return '/tmp/pass52-install-test-'.getmypid().'/'.$s; }
    function now() { return '2026-11-11 00:00:00'; }
    function redirect() { return new class { public function route($name) { return $name; } }; }
    class FakeAdminTable {
        private $db;
        public function __construct($db) { $this->db=$db; }
        public function exists() { $this->db->events[]='exists'; return $this->db->existing; }
        public function insert($row) { $this->db->events[]='insert'; $this->db->inserted=$row; if ($this->db->failInsert) throw new \RuntimeException('insert failure'); }
    }
    class FakeConnection {
        public $events=[],$inserted=null,$existing=false,$failLock=false,$failInsert=false;
        public function selectOne($query) {
            if (str_contains($query,'GET_LOCK')) { $this->events[]='lock'; return (object)['acquired'=>$this->failLock?0:1]; }
            if (str_contains($query,'RELEASE_LOCK')) { $this->events[]='unlock'; return (object)['released'=>1]; }
            throw new \RuntimeException('Unexpected query');
        }
        public function transaction($fn) { $this->events[]='begin'; try { $fn(); $this->events[]='commit'; } catch (\Throwable $e) { $this->events[]='rollback'; throw $e; } }
        public function table($name) { if ($name!=='admins') throw new \RuntimeException('Unexpected table'); return new FakeAdminTable($this); }
    }
    require __DIR__.'/../core/vendor/rachidlaasri/laravel-installer/src/Controllers/AdministratorController.php';
    $checks=0;
    function check($condition,$description) { global $checks; $checks++; if (!$condition) throw new \RuntimeException('FAILED: '.$description); }
    $controller=new \RachidLaasri\LaravelInstaller\Controllers\AdministratorController();
    $db=new FakeConnection(); \Illuminate\Support\Facades\DB::$db=$db;
    $request=new \Illuminate\Http\Request();
    check($controller->store($request)==='LaravelInstaller::final','successful redirect');
    check($db->events===['lock','begin','exists','insert','commit','unlock'],'named lock held across commit');
    check($request->session->get('installer_admin_created')===true,'session flag only after commit');
    check($request->session->get('regenerated')===true,'session regenerated');
    check($db->inserted['email']==='owner@example.invalid','admin content preserved');
    $db=new FakeConnection();$db->existing=true;\Illuminate\Support\Facades\DB::$db=$db;
    $request=new \Illuminate\Http\Request();
    try { $controller->store($request); throw new \RuntimeException('expected duplicate failure'); } catch (\RuntimeException $e) { check($e->getMessage()==='An administrator already exists.','duplicate admin rejected'); }
    check($db->events===['lock','begin','exists','rollback','unlock'],'lock released after rollback');
    check($request->session->get('installer_admin_created')!==true,'no success flag on failure');
    $db=new FakeConnection();$db->failLock=true;\Illuminate\Support\Facades\DB::$db=$db;
    try { $controller->store(new \Illuminate\Http\Request()); throw new \RuntimeException('expected lock failure'); } catch (\RuntimeException $e) { check(str_contains($e->getMessage(),'busy'),'failed lock rejected'); }
    check($db->events===['lock'],'failed lock does not transact or unlock');
    $db=new FakeConnection();$db->failInsert=true;\Illuminate\Support\Facades\DB::$db=$db;
    try { $controller->store(new \Illuminate\Http\Request()); throw new \RuntimeException('expected insert failure'); } catch (\RuntimeException $e) { check($e->getMessage()==='insert failure','insert error propagated'); }
    check($db->events===['lock','begin','exists','insert','rollback','unlock'],'failed insert releases lock after rollback');
    echo "PASS: {$checks} first-admin lock assertions\n";
}
