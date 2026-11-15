<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Services\Sites\ThemeFields;
use App\Services\Sites\SiteDocument;
use App\Services\Sites\SiteTemplates;
use Illuminate\Validation\ValidationException;
class TitanThemeEditableFieldsTest extends TestCase {
 public function test_all_fifteen_templates_have_editable_theme_fields(): void {
  $this->assertCount(15, ThemeFields::catalog());
  foreach(SiteTemplates::catalog() as $key=>$template) {
   $fields=ThemeFields::forTemplate($key);
   $this->assertNotEmpty($fields['copy'], $key);
   $doc=SiteTemplates::make($key);$doc['theme_copy'][array_key_first($fields['copy'])]='Changed by editor';
   $decoded=SiteDocument::decode(SiteDocument::encode($doc),$key);
   $this->assertSame('Changed by editor',$decoded['theme_copy'][array_key_first($fields['copy'])]);
  }
 }
 public function test_unknown_override_is_rejected(): void {
  $doc=SiteTemplates::make('clinar');$doc['theme_copy']=['unknown_key'=>'Bad'];
  $this->expectException(ValidationException::class);
  SiteDocument::decode(SiteDocument::encode($doc),'clinar');
 }
 public function test_javascript_url_for_image_is_rejected(): void {
  $fields=ThemeFields::forTemplate('clinar');$this->assertNotEmpty($fields['images']);
  $doc=SiteTemplates::make('clinar');$doc['theme_images']=[array_key_first($fields['images'])=>'javascript:alert(1)'];
  $this->expectException(ValidationException::class);
  SiteDocument::decode(SiteDocument::encode($doc),'clinar');
 }
}
