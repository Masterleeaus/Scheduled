<?php
namespace Illuminate\Support\Facades {
    class Artisan { public static function call($cmd,$opts) { return 0; } }
    class DB { public static array $installed=[]; public static function table($name) { return new Query; } }
    class Query { public function whereIn($c,$v){return $this;} public function distinct(){return $this;} public function pluck($c){return new Rows;} }
    class Rows { public function all(){return DB::$installed;} }
    class Schema {
        public static string $missingTable=''; public static string $missingColumn='';
        public static function hasTable($t){return $t!==self::$missingTable;}
        public static function hasColumn($t,$c){return ($t.'.'.$c)!==self::$missingColumn;}
    }
}
namespace {
 require __DIR__.'/../core/app/Support/Install/InitialFeatureMigrations.php';
 use App\Support\Install\InitialFeatureMigrations as M;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Facades\Schema;
 $dir=__DIR__.'/../core/database/migrations';
 $files=M::files($dir);
 if(count($files)!==21) exit(1);
 // Every 2126 migration on disk must be listed exactly once, and the list must match the disk.
 $ref=new ReflectionClassConstant(M::class,'REQUIRED_MIGRATIONS');
 $required=$ref->getValue();
 $disk=array_map(fn($f)=>basename($f,'.php'), glob($dir.'/2126_*.php'));
 if(count($required)!==count(array_unique($required))) exit(6);
 if(array_diff($disk,$required)!==[] || array_diff($required,$disk)!==[]) exit(7);
 DB::$installed=array_keys($files); M::verify($dir);
 Schema::$missingTable='booking_request_keys';
 try {M::verify($dir);exit(2);} catch(\RuntimeException $e) {} Schema::$missingTable='';
 Schema::$missingColumn='titan_landing_campaigns.published_snapshot';
 try {M::verify($dir);exit(3);} catch(\RuntimeException $e) {} Schema::$missingColumn='';
 $tmp=sys_get_temp_dir().'/pass46-migrations-'.bin2hex(random_bytes(6)); mkdir($tmp);
 foreach($files as $f) copy($f,$tmp.'/'.basename($f));
 file_put_contents($tmp.'/2126_12_01_000001_unapproved.php','<?php return 1;');
 try {M::files($tmp);exit(4);} catch(\RuntimeException $e) {}
 unlink($tmp.'/2126_12_01_000001_unapproved.php');
 unlink($tmp.'/'.basename(reset($files)));
 try {M::files($tmp);exit(5);} catch(\RuntimeException $e) {}
 foreach(glob($tmp.'/*') as $f) unlink($f); rmdir($tmp);
 echo "PASS: exact 21 migrations, all tables, altered columns, rejects unexpected and missing migrations\n";
}
