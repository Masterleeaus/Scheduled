<?php
namespace Tests\Unit\Landing;
use App\Services\Landing\CampaignDesign;
use App\Services\Landing\CampaignTemplates;
use PHPUnit\Framework\TestCase;
class CampaignDesignTest extends TestCase {
 public function testAllCampaignsHaveValidEditableDefaults(): void {foreach(array_keys(CampaignTemplates::definitions()) as $key){$defaults=CampaignDesign::defaults($key);$this->assertSame($defaults,CampaignDesign::validate($defaults));$this->assertCount(3,$defaults['benefits']);}}
 public function testRoundTrip(): void {$data=CampaignDesign::defaults('commercial');$data['theme']='editorial';$this->assertSame($data,CampaignDesign::decode(json_encode($data),'commercial'));}
 public function testMissingLegacyDesignFallsBackToDefaults(): void {$this->assertSame(CampaignDesign::defaults('office'),CampaignDesign::decode(null,'office'));}
}
