<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Services\Sites\SiteDocument;
use App\Services\Sites\SiteOnboarding;
use App\Services\Sites\SiteTemplates;
class SiteOnboardingTest extends TestCase {
 public function test_all_website_templates_create_editable_validated_business_drafts(): void {
  foreach(array_keys(SiteTemplates::catalog()) as $template){
   $doc=SiteOnboarding::build($template,['business_name'=>'Example Cleaning Co','business_summary'=>'Professional cleaning across Melbourne.','service_area'=>'Melbourne','contact_email'=>'hello@example.test','tone'=>'friendly']);
   $this->assertSame('Example Cleaning Co',$doc['headline']);
   $this->assertSame('hello@example.test',$doc['contact_email']);
   $this->assertSame('Melbourne',$doc['service_areas'][0]['name']);
   $this->assertSame($doc,SiteDocument::decode(SiteDocument::encode($doc),$template));
  }
 }
 public function test_generation_routes_are_registered(): void {
  $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.sites.creation-preview'));
  $this->assertTrue(\Illuminate\Support\Facades\Route::has('admin.sites.store'));
 }
}
