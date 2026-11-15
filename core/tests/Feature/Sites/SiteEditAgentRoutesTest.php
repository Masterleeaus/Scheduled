<?php
namespace Tests\Feature\Sites;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
class SiteEditAgentRoutesTest extends TestCase {
 public function test_edit_proposal_route_is_registered(): void {
  $route=Route::getRoutes()->getByName('admin.sites.site-edit-propose');
  $this->assertNotNull($route);
  $this->assertContains('POST',$route->methods());
  $this->assertContains('throttle:8,1',$route->gatherMiddleware());
 }
}
