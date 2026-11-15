<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('titan_agent_audit_events',function(Blueprint $table){
   $table->bigIncrements('id');$table->unsignedBigInteger('site_id');$table->unsignedBigInteger('tenant_id')->nullable();
   $table->unsignedBigInteger('proposal_id');$table->unsignedBigInteger('admin_id');
   $table->string('action',24);$table->string('detail',80);$table->unsignedBigInteger('source_revision');
   $table->timestamp('created_at');$table->index(['site_id','id']);$table->index(['tenant_id','id']);
  });
 }
 public function down():void {Schema::dropIfExists('titan_agent_audit_events');}
};
