<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuilderRevisions extends Migration
{
    public function up()
    {
        Schema::create('builder_revisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('target_type', 16);
            $table->unsignedBigInteger('target_id');
            $table->unsignedBigInteger('language_id');
            $table->char('revision', 64);
            $table->longText('html')->nullable();
            $table->longText('css')->nullable();
            $table->longText('components')->nullable();
            $table->longText('styles')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unique(['target_type','target_id','language_id','revision'], 'builder_revision_unique');
            $table->index(['target_type','target_id','language_id','id'], 'builder_revision_lookup');
        });
    }

    public function down()
    {
        Schema::dropIfExists('builder_revisions');
    }
}
