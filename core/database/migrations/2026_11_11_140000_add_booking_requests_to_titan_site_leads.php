<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::table('titan_site_leads',function(Blueprint $t){
  $t->string('request_kind',32)->default('enquiry');
  $t->date('preferred_date')->nullable();
  $t->time('preferred_time')->nullable();
  $t->text('booking_details')->nullable();
  $t->index(['site_id','request_kind','preferred_date'],'titan_site_leads_request_schedule_idx');
 }); }
 public function down(): void { Schema::table('titan_site_leads',function(Blueprint $t){
  $t->dropIndex('titan_site_leads_request_schedule_idx');
  $t->dropColumn(['request_kind','preferred_date','preferred_time','booking_details']);
 }); }
};
