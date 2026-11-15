<?php
// Standalone behavioral regression for the production middleware, with minimal facade stubs.
namespace Illuminate\Support\Facades {
    class Auth {
        public static $guard;
        public static function guard($name) { if ($name !== 'admin') throw new \Exception('guard'); return self::$guard; }
    }
    class Session { public static $flashes = []; public static function flash($key,$message) { self::$flashes[$key]=$message; } }
}
namespace {
    class FakeGuard {
        public $admin; public $loggedOut=false;
        public function __construct($admin) {$this->admin=$admin;}
        public function user() {return $this->admin;}
        public function logout() {$this->loggedOut=true;}
    }
    class FakeSession {
        public $invalidated=false; public $token=false;
        public function invalidate() {$this->invalidated=true;}
        public function regenerateToken() {$this->token=true;}
    }
    class FakeRequest {
        public $session;
        public function __construct() {$this->session=new FakeSession;}
        public function session() {return $this->session;}
    }
    function redirect() { return new class { public function route($name) { return $name; } }; }
    require __DIR__.'/../core/app/Http/Middleware/CheckStatus.php';
    $cases = [
        ['role_id'=>null,'status'=>1,'expect'=>true],
        ['role_id'=>null,'status'=>0,'expect'=>false],
        ['role_id'=>3,'status'=>0,'expect'=>false],
        ['role_id'=>3,'status'=>1,'expect'=>true],
    ];
    $passed=0;
    foreach($cases as $case) {
        $guard=new FakeGuard((object)['role_id'=>$case['role_id'],'status'=>$case['status']]);
        \Illuminate\Support\Facades\Auth::$guard=$guard;
        $req=new FakeRequest;
        $nextCalled=false;
        $result=(new \App\Http\Middleware\CheckStatus)->handle($req,function()use(&$nextCalled){$nextCalled=true;return 'continued';});
        if ($nextCalled !== $case['expect']) throw new \Exception('Unexpected access result');
        if (!$case['expect'] && (!$guard->loggedOut || !$req->session->invalidated || !$req->session->token || $result !== 'admin.login')) throw new \Exception('Inactive logout incomplete');
        ++$passed;
    }
    \Illuminate\Support\Facades\Auth::$guard=new FakeGuard(null);
    $req=new FakeRequest;
    $result=(new \App\Http\Middleware\CheckStatus)->handle($req,fn()=> 'anonymous-pass');
    if($result !== 'anonymous-pass') throw new \Exception('Guest behavior changed');
    ++$passed;
    echo "PASS: $passed behavioral middleware scenarios\n";
}
