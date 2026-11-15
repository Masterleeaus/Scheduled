<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateTitanLandingLeads extends Migration {
 public function up() { Schema::create('titan_landing_leads', function(Blueprint $t) {
  $t->bigIncrements('id'); $t->unsignedBigInteger('campaign_id')->index();
  $t->unsignedBigInteger('language_id')->index(); $t->string('name',120); $t->string('email',254);
  $t->string('phone',40)->nullable(); $t->text('message')->nullable();
  $t->string('utm_source',120)->nullable(); $t->string('utm_medium',120)->nullable();
  $t->string('utm_campaign',120)->nullable(); $t->boolean('consent');
  $t->string('status',20)->default('new'); $t->timestamps();
  $t->foreign('campaign_id')->references('id')->on('titan_landing_campaigns')->onDelete('cascade');
 }); }
 public function down() { Schema::dropIfExists('titan_landing_leads'); }
}
