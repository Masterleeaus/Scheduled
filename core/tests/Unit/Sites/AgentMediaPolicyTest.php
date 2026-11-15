<?php
namespace Tests\Unit\Sites;
use App\Services\Sites\AgentMediaPolicy;
use PHPUnit\Framework\TestCase;
final class AgentMediaPolicyTest extends TestCase {
 public function test_rejects_foreign_site_media():void {
  $url='/uploads/site-media/12/'.str_repeat('a',40).'.png';
  $this->assertTrue(AgentMediaPolicy::belongsToSite($url,12));
  $this->assertFalse(AgentMediaPolicy::belongsToSite($url,13));
  $this->assertFalse(AgentMediaPolicy::belongsToSite('/uploads/site-media/12/../../secret.png',12));
 }
}
