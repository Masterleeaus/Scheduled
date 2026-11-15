<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('titan_brand_proposals',function(Blueprint $table){
   $table->bigIncrements('id');$table->unsignedBigInteger('site_id');$table->unsignedBigInteger('language_id');
   $table->unsignedBigInteger('source_revision');$table->string('kind',24);
   $table->longText('changes');$table->text('rationale')->nullable();
   $table->string('status',16)->default('pending');$table->timestamps();
   $table->index(['site_id','language_id','status']);
  });
 }
 public function down(): void {Schema::dropIfExists('titan_brand_proposals');}
};
