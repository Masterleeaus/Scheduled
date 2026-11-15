<?php
namespace Tests\Feature;
use Tests\TestCase;
class LandingCampaignRoutesTest extends TestCase {
 public function test_unpublished_unknown_landing_route_returns_not_found() { $this->get('/campaign/a-campaign-that-does-not-exist')->assertStatus(404); }
 public function test_public_submission_requires_csrf() { $this->post('/campaign/a-campaign-that-does-not-exist/leads',['name'=>'Test'])->assertStatus(404); }
}
