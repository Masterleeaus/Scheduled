<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Services\Landing\PublishedCampaign;
class LandingPublishedSnapshotTest extends TestCase {
 public function test_public_snapshot_does_not_follow_new_draft_changes(): void {
  $row=(object)['name'=>'Test','slug'=>'live-slug','published_slug'=>'live-slug','headline'=>'Original','design_json'=>'{}'];
  $row->published_snapshot=PublishedCampaign::snapshot($row);
  $row->headline='Unpublished draft headline';$row->slug='new-draft-slug';
  $rendered=PublishedCampaign::render($row);
  $this->assertSame('Original',$rendered->headline);
  $this->assertSame('live-slug',$rendered->slug);
 }
 public function test_snapshot_rejects_missing_published_content(): void {
  $this->expectException(\RuntimeException::class);
  PublishedCampaign::render((object)['published_snapshot'=>null]);
 }
}
