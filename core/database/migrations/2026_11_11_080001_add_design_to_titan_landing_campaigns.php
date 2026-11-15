<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {Schema::table('titan_landing_campaigns',function(Blueprint $table){$table->text('design_json')->nullable();});}
 public function down(): void {Schema::table('titan_landing_campaigns',function(Blueprint $table){$table->dropColumn('design_json');});}
};
