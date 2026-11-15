<?php
namespace Illuminate\Support\Facades {
    class Artisan {
        public static $exit=0;
        public static $invocation=[];
        public static function call($name,$options){self::$invocation=[$name,$options];return self::$exit;}
    }
    class DB {
        public static $installed=[];
        public static function table($table){if($table!=='migrations')throw new \RuntimeException('Unexpected table');return new FakeQuery;}
    }
    class FakeQuery {
        public function whereIn($col,$values){if($col!=='migration')throw new \RuntimeException('Unexpected column');return $this;}
        public function distinct(){return $this;}
        public function pluck($col){return new FakeRows;}
    }
    class FakeRows {
        public function all(){return DB::$installed;}
    }
    class Schema {
        public static $missing=''; public static $missingColumn='';
        public static function hasTable($table){return $table!==self::$missing;}
        public static function hasColumn($table,$column){return ($table.'.'.$column)!==self::$missingColumn;}
    }
}
namespace {
    require __DIR__.'/../core/app/Support/Install/InitialFeatureMigrations.php';
    $dir=__DIR__.'/../core/database/migrations';
    $files=\App\Support\Install\InitialFeatureMigrations::files($dir);
    \Illuminate\Support\Facades\DB::$installed=array_keys($files);
    \App\Support\Install\InitialFeatureMigrations::apply($dir);
    [$name,$options]=\Illuminate\Support\Facades\Artisan::$invocation;
    if($name!=='migrate'||count($options['--path'])!==20||empty($options['--force'])||empty($options['--realpath'])) exit(1);
    foreach ($options['--path'] as $path){if(!str_contains($path, '/2026_'))exit(2);}
    \Illuminate\Support\Facades\DB::$installed=array_slice(array_keys($files),0,-1);
    try{\App\Support\Install\InitialFeatureMigrations::verify($dir);exit(3);}catch(\RuntimeException $e){}
    \Illuminate\Support\Facades\DB::$installed=array_keys($files);
    \Illuminate\Support\Facades\Schema::$missing='titan_sites';
    try{\App\Support\Install\InitialFeatureMigrations::verify($dir);exit(4);}catch(\RuntimeException $e){}
    \Illuminate\Support\Facades\Schema::$missing='';
    \Illuminate\Support\Facades\Artisan::$exit=1;
    try{\App\Support\Install\InitialFeatureMigrations::apply($dir);exit(5);}catch(\RuntimeException $e){}
    echo "PASS: scoped migration call, missing ledger, missing table, command failure\n";
}
