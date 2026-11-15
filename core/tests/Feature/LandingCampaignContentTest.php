<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Services\Landing\CampaignDesign;
class LandingCampaignContentTest extends TestCase {
 public function test_form_content_is_editable_and_round_trips(): void {
  $d=CampaignDesign::fromInput(['form_heading'=>'Request a callback','form_intro'=>'Tell us more','form_button'=>'Contact us','consent_label'=>'Please contact me','footer_text'=>'Example Brand'],'commercial');
  $restored=CampaignDesign::decode(json_encode($d),'commercial');
  $this->assertSame('Request a callback',$restored['form_heading']);
  $this->assertSame('Example Brand',$restored['footer_text']);
 }
 public function test_form_fields_reject_oversized_values(): void {
  $this->expectException(\Illuminate\Validation\ValidationException::class);
  CampaignDesign::fromInput(['form_heading'=>str_repeat('X',121)],'commercial');
 }
}
