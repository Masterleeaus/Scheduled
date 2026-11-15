<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateTitanBusinessTenants extends Migration {
 public function up() {
  Schema::create('titan_business_tenants',function(Blueprint $t){
   $t->bigIncrements('id');$t->string('name',120);$t->string('status',20)->default('active');$t->timestamps();
  });
  Schema::create('titan_business_memberships',function(Blueprint $t){
   $t->bigIncrements('id');$t->unsignedBigInteger('tenant_id');$t->unsignedBigInteger('admin_id');$t->string('role',24)->default('editor');$t->timestamps();
   $t->unique(['tenant_id','admin_id'],'titan_tenant_member_unique');$t->index(['admin_id','tenant_id']);
   $t->foreign('tenant_id')->references('id')->on('titan_business_tenants')->onDelete('cascade');
  });
  Schema::table('titan_sites',function(Blueprint $t){$t->unsignedBigInteger('tenant_id')->nullable()->index();});
  Schema::table('titan_landing_campaigns',function(Blueprint $t){$t->unsignedBigInteger('tenant_id')->nullable()->index();});
 }
 public function down() {
  Schema::table('titan_landing_campaigns',function(Blueprint $t){$t->dropColumn('tenant_id');});
  Schema::table('titan_sites',function(Blueprint $t){$t->dropColumn('tenant_id');});
  Schema::dropIfExists('titan_business_memberships');Schema::dropIfExists('titan_business_tenants');
 }
}
