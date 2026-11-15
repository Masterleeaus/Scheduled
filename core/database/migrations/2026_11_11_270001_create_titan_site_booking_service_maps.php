<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('titan_site_booking_service_maps',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('site_id')->unique();$t->unsignedBigInteger('tenant_id')->index();
   $t->unsignedBigInteger('owner_user_id');$t->json('service_ids');$t->timestamp('updated_at')->nullable();
  });
 }
 public function down():void {Schema::dropIfExists('titan_site_booking_service_maps');}
};
