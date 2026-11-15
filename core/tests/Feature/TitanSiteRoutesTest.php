<?php
namespace Tests\Feature;
use Tests\TestCase;
class TitanSiteRoutesTest extends TestCase {
 public function test_public_site_route_exists() { $this->assertNotNull(app('router')->getRoutes()->getByName('front.sites.show')); }
 public function test_admin_site_routes_exist() {
  foreach(['admin.sites.index','admin.sites.create','admin.sites.store','admin.sites.edit','admin.sites.update','admin.sites.publish','admin.sites.restore','admin.sites.history','admin.sites.preview'] as $name)
   $this->assertNotNull(app('router')->getRoutes()->getByName($name),$name);
 }
}
