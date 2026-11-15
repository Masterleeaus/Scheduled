<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateTitanSiteDomains extends Migration {
 public function up(){Schema::create('titan_site_domains',function(Blueprint $t){
  $t->bigIncrements('id');$t->unsignedBigInteger('tenant_id')->index();$t->string('hostname',253)->unique();
  $t->string('target_type',16);$t->unsignedBigInteger('target_id');$t->string('verification_token',80);
  $t->timestamp('verified_at')->nullable();$t->timestamp('activated_at')->nullable();$t->timestamps();
  $t->index(['tenant_id','target_type','target_id'],'titan_domain_target_idx');
  $t->foreign('tenant_id')->references('id')->on('titan_business_tenants')->onDelete('cascade');
 });}
 public function down(){Schema::dropIfExists('titan_site_domains');}
}
