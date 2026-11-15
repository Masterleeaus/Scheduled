<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {Schema::create('booking_public_domains',function(Blueprint $table){$table->bigIncrements('id');$table->unsignedBigInteger('owner_user_id')->index();$table->string('domain',253)->unique();$table->string('status',16)->default('pending')->index();$table->timestamp('verified_at')->nullable();$table->timestamps();});}
 public function down():void {Schema::dropIfExists('booking_public_domains');}
};
