<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {Schema::create('titan_site_booking_settings',function(Blueprint $table){
  $table->bigIncrements('id');$table->unsignedBigInteger('site_id')->unique();
  $table->unsignedBigInteger('tenant_id')->index();$table->unsignedBigInteger('owner_user_id');$table->string('hostname',253);
  $table->boolean('enabled')->default(false);$table->timestamps();
  $table->foreign('site_id')->references('id')->on('titan_sites')->onDelete('cascade');
  $table->index(['tenant_id','owner_user_id'],'site_booking_owner_idx');
 });}
 public function down():void {Schema::dropIfExists('titan_site_booking_settings');}
};
