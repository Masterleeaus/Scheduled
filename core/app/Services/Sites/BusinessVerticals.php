<?php
namespace App\Services\Sites;
use Illuminate\Validation\ValidationException;
/** A single data-driven catalogue for onboarding, template recommendations and AI context. */
final class BusinessVerticals {
 private static ?array $index=null;
 public static function all(): array {
  if(self::$index!==null)return self::$index;
  $path=resource_path('data/business-verticals.json');
  $decoded=json_decode((string)file_get_contents($path),true,20,JSON_THROW_ON_ERROR);
  if(!isset($decoded['verticals'])||count($decoded['verticals'])!==50)throw new \RuntimeException('Business vertical catalogue must contain 50 entries');
  $rows=[];
  foreach($decoded['verticals'] as $row){
   if(!isset($row['key'],$row['name'],$row['group'],$row['recommended_template'])||isset($rows[$row['key']]))throw new \RuntimeException('Invalid or duplicate vertical');
   $rows[$row['key']]=$row;
  }
  return self::$index=$rows;
 }
 public static function find(?string $key): ?array {return $key===null||$key===''?null:(self::all()[$key]??null);}
 public static function require(string $key): array {
  $result=self::find($key);
  if(!$result)throw ValidationException::withMessages(['vertical_key'=>'Unknown business vertical.']);
  return $result;
 }
 public static function groups(): array {
  $groups=[];foreach(self::all() as $row)$groups[$row['group']][$row['key']]=$row['name'];return $groups;
 }
 public static function recommend(string $key): string {return self::require($key)['recommended_template'];}
 public static function forAi(?string $key): ?array {
  $row=self::find($key);
  return $row?['key'=>$row['key'],'name'=>$row['name'],'category'=>$row['group']]:null;
 }
 public static function safeServiceSeed(string $key): array {
  $row=self::require($key);
  // Do not invent credentials, price, licensing, guarantees, professional medical advice or regulated work.
  return [['name'=>$row['name'],'description'=>'Ask about available '.$row['name'].' services, scope and scheduling in your area.']];
 }
}
