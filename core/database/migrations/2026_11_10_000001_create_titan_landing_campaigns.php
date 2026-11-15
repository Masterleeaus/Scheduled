<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateTitanLandingCampaigns extends Migration {
 public function up() { Schema::create('titan_landing_campaigns', function(Blueprint $t) {
  $t->bigIncrements('id'); $t->unsignedBigInteger('language_id')->index();
  $t->string('name',120); $t->string('slug',100)->unique(); $t->string('template',40);
  $t->string('headline',180); $t->text('subheading')->nullable(); $t->string('cta_label',80);
  $t->string('accent',7)->default('#173c66'); $t->string('phone',40)->nullable();
  $t->string('meta_title',160)->nullable(); $t->string('meta_description',300)->nullable();
  $t->boolean('published')->default(false)->index(); $t->timestamp('published_at')->nullable();
  $t->unsignedBigInteger('revision')->default(1); $t->timestamps();
 }); }
 public function down() { Schema::dropIfExists('titan_landing_campaigns'); }
}
