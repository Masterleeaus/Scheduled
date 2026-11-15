<?php
namespace Tests\Unit\Sites;
use App\Services\Sites\{SiteDocument,SiteTemplates,SiteEditOperations};
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
class SiteEditOperationsTest extends TestCase {
 private function doc(): array {return SiteDocument::decode(SiteDocument::encode(SiteTemplates::make('residential')),'residential');}
 public function test_changes_are_persistent_and_preserve_unedited_fields(): void {
  $before=$this->doc();$after=SiteEditOperations::apply($before,[['action'=>'set_text','field'=>'headline','value'=>'Professional home cleaning']],'residential');
  $this->assertSame('Professional home cleaning',$after['headline']);$this->assertSame($before['services'],$after['services']);
 }
 public function test_disallows_unknown_field(): void {$this->expectException(ValidationException::class);SiteEditOperations::apply($this->doc(),[['action'=>'set_text','field'=>'script','value'=>'x']],'residential');}
 public function test_disallows_unknown_action(): void {$this->expectException(ValidationException::class);SiteEditOperations::apply($this->doc(),[['action'=>'execute','field'=>'headline','value'=>'x']],'residential');}
 public function test_disallows_out_of_range_collection_index(): void {$this->expectException(ValidationException::class);SiteEditOperations::apply($this->doc(),[['action'=>'replace_item','collection'=>'services','index'=>999,'field'=>'name','value'=>'x']],'residential');}
 public function test_disallows_more_than_twelve_operations(): void {$this->expectException(ValidationException::class);SiteEditOperations::apply($this->doc(),array_fill(0,13,['action'=>'set_text','field'=>'headline','value'=>'x']),'residential');}
}
