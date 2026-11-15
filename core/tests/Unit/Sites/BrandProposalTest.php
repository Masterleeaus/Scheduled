<?php
namespace Tests\Unit\Sites;
use PHPUnit\Framework\TestCase;
use App\Services\Sites\BrandProposal;
final class BrandProposalTest extends TestCase {
 public function test_rejects_unknown_tool_operation(): void {
  $this->expectException(\Illuminate\Validation\ValidationException::class);
  BrandProposal::validate(['brand'=>[]],['sql'=>'DROP TABLE sites'],'residential');
 }
 public function test_rejects_external_asset(): void {
  $this->expectException(\Illuminate\Validation\ValidationException::class);
  BrandProposal::validate([],['seo_image'=>'https://untrusted.example/image.jpg'],'residential');
 }
 public function test_accepts_generated_local_asset(): void {
  $changes=BrandProposal::validate([],['seo_image'=>'/uploads/site-media/12/'.str_repeat('a',40).'.png'],'residential');
  $this->assertArrayHasKey('seo_image',$changes);
 }
}
