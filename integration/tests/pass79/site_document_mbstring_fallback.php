<?php
// Executable without Laravel: only the exception path needs a tiny stub.
namespace Illuminate\Validation { class ValidationException extends \Exception { public static function withMessages(array $data): self { return new self(json_encode($data)); } } }
namespace {
 require __DIR__.'/../../../core/app/Services/Sites/SiteDocument.php';
 $class='App\\Services\\Sites\\SiteDocument';
 $cases=['plain ASCII'=>['abc',3],'multi-byte accented'=>['Café',4],'emoji'=>['A😀B',3],'empty'=>['',0]];
 $n=0;
 foreach($cases as $label=>$values){[$text,$length]=$values;if($class::characterLength($text)!==$length)throw new \RuntimeException("Failed: $label");$n++;}
 try{$class::characterLength("\xC3\x28");throw new \RuntimeException('Accepted malformed UTF-8');}catch(\Illuminate\Validation\ValidationException $e){$n++;}
 echo "PASS $n character-length assertions\n";
}
