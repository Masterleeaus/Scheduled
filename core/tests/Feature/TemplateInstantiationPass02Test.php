<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Services\Sites\SiteTemplates;
use App\Services\Sites\SiteDocument;
use App\Services\Sites\TemplateRegistry;
use App\Services\Landing\CampaignTemplates;
class TemplateInstantiationPass02Test extends TestCase {
 public function test_every_website_template_has_renderable_assets_and_valid_document(): void {
  $catalog=TemplateRegistry::sites();$this->assertCount(15,$catalog);
  foreach($catalog as $key=>$meta) {
   $this->assertTrue($meta['available'],$key.' assets incomplete');
   $this->assertFileExists($meta['preset_path']);
   $doc=SiteTemplates::make($key);
   $this->assertIsArray(SiteDocument::decode(SiteDocument::encode($doc)));
  }
 }
 public function test_every_landing_template_is_registered(): void {
  $this->assertCount(10,TemplateRegistry::campaigns());
  foreach(CampaignTemplates::definitions() as $key=>$metadata) {
   $this->assertSame($metadata['title'],TemplateRegistry::campaign($key)['title']);
  }
 }
 public function test_missing_keys_are_rejected(): void {
  $this->assertNull(SiteTemplates::make('missing-template'));
  $this->assertNull(TemplateRegistry::campaign('missing-template'));
 }
}
