<?php
require __DIR__.'/../../app/Services/Sites/SiteCleantoServiceMapping.php';
use App\Services\Sites\SiteCleantoServiceMapping as M;
$c=['services'=>[['id'=>10,'label'=>'Pet grooming'],['id'=>20,'label'=>'Dog training']]];
function check($value,$expected,$name){if($value!==$expected)throw new RuntimeException('Failed: '.$name);}
check(M::validateSelection(['20','10','20'],$c),[10,20],'dedupe and sorting');
check(M::validForCatalogue([20],$c),[20],'existing mapping');
check(M::validForCatalogue([99],$c),[],'deleted service invalidates mapping');
foreach([[99],[],['-1'],['10 OR 1=1'],[0],['0010']] as $bad){try{M::validateSelection($bad,$c);throw new RuntimeException('Accepted invalid ID');}catch(InvalidArgumentException $e){}}
echo "Site/Cleanto service mapping checks passed\n";
