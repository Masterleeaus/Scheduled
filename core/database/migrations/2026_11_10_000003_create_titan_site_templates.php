<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateTitanSiteTemplates extends Migration {
 public function up() {
  Schema::create('titan_sites',function(Blueprint $t){
   $t->bigIncrements('id'); $t->unsignedBigInteger('language_id'); $t->string('slug',100);
   $t->string('name',120); $t->string('template_key',40); $t->longText('draft_document');
   $t->longText('published_document')->nullable(); $t->boolean('published')->default(false);
   $t->unsignedBigInteger('revision')->default(1); $t->timestamp('published_at')->nullable();$t->timestamps();
   $t->unique(['language_id','slug'],'titan_sites_language_slug_unique');$t->index(['language_id','published']);
  });
  Schema::create('titan_site_revisions',function(Blueprint $t){
   $t->bigIncrements('id');$t->unsignedBigInteger('site_id');$t->unsignedBigInteger('revision');$t->longText('document');
   $t->string('action',20);$t->timestamp('created_at');$t->unique(['site_id','revision'],'titan_site_revision_unique');
   $t->foreign('site_id')->references('id')->on('titan_sites')->onDelete('cascade');
  });
 }
 public function down() {Schema::dropIfExists('titan_site_revisions');Schema::dropIfExists('titan_sites');}
}
