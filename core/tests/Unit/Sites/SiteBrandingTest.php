<?php
namespace Tests\Unit\Sites;
use App\Services\Sites\SiteBranding;
use App\Services\Sites\SiteBrandAudit;
use Tests\TestCase;
class SiteBrandingTest extends TestCase {
 public function test_brand_defaults_are_valid(): void { $doc=['accent'=>'#123456']; SiteBranding::normalize($doc);$this->assertEquals('#123456',$doc['brand']['primary']);$this->assertStringContainsString('--brand-primary:#123456',SiteBranding::css($doc)); }
 public function test_rejects_css_injection(): void { $this->expectException(\Illuminate\Validation\ValidationException::class);$doc=['accent'=>'#123456','brand'=>['primary'=>'red;position:fixed']];SiteBranding::normalize($doc); }
 public function test_rejects_unknown_font(): void { $this->expectException(\Illuminate\Validation\ValidationException::class);$doc=['accent'=>'#123456','brand'=>['font'=>'url(evil)']];SiteBranding::normalize($doc); }
 public function test_high_priority_contrast_and_contact_audit(): void { $report=SiteBrandAudit::inspect(['accent'=>'#000000','brand'=>['ink'=>'#ffffff','background'=>'#ffffff'],'seo_description'=>'ok'],'residential');$this->assertGreaterThan(0,$report['high']); }
}
