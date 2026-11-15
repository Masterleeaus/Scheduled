<?php
namespace Tests\Unit\Sites;
use App\Services\Sites\SiteImages;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
class SiteImagesTest extends TestCase {
 public function test_image_block_is_valid(): void {
  $block=['id'=>'image-123456','url'=>'/uploads/site-media/42/picture.webp','alt'=>'A clean office','selector'=>'main > section:nth-of-type(2)','index'=>0,'position'=>'after','width'=>'wide'];
  $this->assertSame([$block],SiteImages::validateBlocks([$block]));
 }
 public function test_unsafe_urls_are_rejected(): void {
  $this->expectException(ValidationException::class);
  SiteImages::validateBlocks([['id'=>'image-123456','url'=>'javascript:alert(1)','alt'=>'','selector'=>'main','index'=>0,'position'=>'after','width'=>'normal']]);
 }
 public function test_duplicate_ids_are_rejected(): void {
  $this->expectException(ValidationException::class);
  $block=['id'=>'image-123456','url'=>'/a.jpg','alt'=>'','selector'=>'main','index'=>0,'position'=>'after','width'=>'normal'];
  SiteImages::validateBlocks([$block,$block]);
 }
}
