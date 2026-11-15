<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Services\Landing\PublishedCampaign;
return new class extends Migration {
 public function up(): void {
  Schema::table('titan_landing_campaigns',function(Blueprint $table){
   $table->string('published_slug',100)->nullable()->unique();
   $table->longText('published_snapshot')->nullable();
  });
  // Existing published campaigns retain the same public content until explicitly republished.
  DB::table('titan_landing_campaigns')->where('published',1)->orderBy('id')->chunkById(100,function($rows){
   foreach($rows as $row) DB::table('titan_landing_campaigns')->where('id',$row->id)->update([
    'published_slug'=>$row->slug,'published_snapshot'=>PublishedCampaign::snapshot($row)
   ]);
  });
 }
 public function down(): void {
  Schema::table('titan_landing_campaigns',function(Blueprint $table){$table->dropUnique(['published_slug']);$table->dropColumn(['published_slug','published_snapshot']);});
 }
};
