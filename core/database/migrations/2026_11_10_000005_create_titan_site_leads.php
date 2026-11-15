<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateTitanSiteLeads extends Migration {
 public function up() { Schema::create('titan_site_leads',function(Blueprint $t){
  $t->bigIncrements('id');$t->unsignedBigInteger('site_id');$t->unsignedBigInteger('language_id');
  $t->string('name',120);$t->string('email',160);$t->string('phone',40)->nullable();
  $t->string('service',120)->nullable();$t->text('message');$t->timestamp('consented_at');$t->timestamps();
  $t->foreign('site_id')->references('id')->on('titan_sites')->onDelete('cascade');
  $t->index(['site_id','created_at']);$t->index(['language_id','created_at']);
 }); }
 public function down(){Schema::dropIfExists('titan_site_leads');}
}
