<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddAttributionToTitanSiteLeads extends Migration {
 public function up() {Schema::table('titan_site_leads',function(Blueprint $t){$t->string('utm_source',100)->nullable();$t->string('utm_medium',100)->nullable();$t->string('utm_campaign',100)->nullable();$t->string('promotion_code',40)->nullable();});}
 public function down(){Schema::table('titan_site_leads',function(Blueprint $t){$t->dropColumn(['utm_source','utm_medium','utm_campaign','promotion_code']);});}
}
