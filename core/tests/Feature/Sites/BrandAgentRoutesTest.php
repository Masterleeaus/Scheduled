<?php
namespace Tests\Feature\Sites;
use Tests\TestCase;
final class BrandAgentRoutesTest extends TestCase {
 public function test_public_user_cannot_access_brand_agent(): void {
  $response=$this->get('/admin/site-starters/1/branding-agent?language_id=1');
  $this->assertNotEquals(200,$response->status());
 }
}
