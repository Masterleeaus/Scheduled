<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Sites\{BrandProvider,BrandProposal,BrandMedia,SiteBrandAudit,SiteDocument};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\Tenants\TenantAccess;
use App\Services\Sites\AgentAudit;
use App\Services\Sites\AgentMediaPolicy;

/** Administrative, permission-gated proposal-review workflow. */
final class SiteBrandAgentController extends Controller {
 private function site(int $id,int $language) {
  $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$language)->first();abort_unless($site,404);if($site->tenant_id) TenantAccess::require((int)$site->tenant_id);return $site;
 }
 public function index(Request $request,int $id) {
  $language=(int)$request->query('language_id');$site=$this->site($id,$language);
  $document=SiteDocument::decode($site->draft_document,$site->template_key);
  $audit=SiteBrandAudit::inspect($document,$site->template_key);
  $proposals=DB::table('titan_brand_proposals')->where('site_id',$id)->where('language_id',$language)->orderByDesc('id')->limit(25)->get();
  return response()->view('admin.sites.brand-agent',compact('site','language','document','audit','proposals'))->header('Cache-Control','private,no-store');
 }
 public function propose(Request $request,int $id) {
  $data=$request->validate(['language_id'=>'required|integer|min:1','revision'=>'required|integer|min:1','instruction'=>'required|string|min:5|max:1200']);
  $site=$this->site($id,(int)$data['language_id']);
  if((int)$site->revision!==(int)$data['revision'])throw ValidationException::withMessages(['revision'=>'Site changed; refresh first.']);
  $doc=SiteDocument::decode($site->draft_document,$site->template_key);
  $result=BrandProvider::propose($doc,SiteBrandAudit::inspect($doc,$site->template_key),$data['instruction']);
  $changes=BrandProposal::validate($doc,['brand'=>$result['brand']],$site->template_key);
  $this->assertFresh($site);
  $this->record($site,'branding',$changes,$result['rationale']);
  return redirect()->route('admin.sites.brand-agent',['id'=>$id,'language_id'=>$site->language_id])->with('success','Brand proposal created for review.');
 }
 public function proposeEdit(Request $request,int $id) {
  $data=$request->validate(['language_id'=>'required|integer|min:1','revision'=>'required|integer|min:1','instruction'=>'required|string|min:5|max:1200']);
  $site=$this->site($id,(int)$data['language_id']);
  if((int)$site->revision!==(int)$data['revision'])throw ValidationException::withMessages(['revision'=>'Site changed; refresh before requesting new edits.']);
  $doc=SiteDocument::decode($site->draft_document,$site->template_key);
  $result=\App\Services\Sites\SiteEditProvider::propose($doc,$site->template_key,$data['instruction']);
  $changes=BrandProposal::validate($doc,['operations'=>$result['operations']],$site->template_key);
  // Recheck revision after external provider call; stale proposals are never stored.
  $current=$this->site($id,(int)$data['language_id']);
  if((int)$current->revision!==(int)$site->revision)throw ValidationException::withMessages(['revision'=>'Draft changed while AI was responding.']);
  $this->assertFresh($site);
  $this->record($site,'website-edit',$changes,$result['rationale']);
  return redirect()->route('admin.sites.brand-agent',['id'=>$id,'language_id'=>$site->language_id])->with('success','Website edits proposed. Review and approve; publishing remains separate.');
 }
 public function generateImage(Request $request,int $id) {
  $data=$request->validate(['language_id'=>'required|integer|min:1','revision'=>'required|integer|min:1','prompt'=>'required|string|min:10|max:1000','target'=>'required|string|in:seo_image,theme_image','field'=>'nullable|string|max:100']);
  $site=$this->site($id,(int)$data['language_id']);
  if((int)$site->revision!==(int)$data['revision'])throw ValidationException::withMessages(['revision'=>'Site changed; refresh first.']);
  $doc=SiteDocument::decode($site->draft_document,$site->template_key);
  $description='Business: '.mb_substr($doc['headline'],0,150).'. '.$data['prompt'];
  // Validate destination before paying for a provider request.
  if($data['target']==='theme_image') {
   $fields=\App\Services\Sites\ThemeFields::forTemplate($site->template_key);
   if(!array_key_exists($data['field']??'', $fields['images']??[]))throw ValidationException::withMessages(['field'=>'Select a valid template image field.']);
  }
  $bytes=BrandProvider::image($description);
  $url=BrandMedia::save($id,$bytes);
  $changes=$data['target']==='seo_image'?['seo_image'=>$url]:['theme_image'=>['field'=>$data['field']??'','url'=>$url]];
  $changes=BrandProposal::validate($doc,$changes,$site->template_key);
  $this->assertFresh($site);
  $this->record($site,'image',$changes,'Generated image: '.mb_substr($data['prompt'],0,250));
  return redirect()->route('admin.sites.brand-agent',['id'=>$id,'language_id'=>$site->language_id])->with('success','Generated asset saved; review and approve to apply.');
 }
 public function decide(Request $request,int $id,int $proposalId) {
  $data=$request->validate(['language_id'=>'required|integer|min:1','revision'=>'required|integer|min:1','decision'=>'required|in:approve,reject']);
  $this->site($id,(int)$data['language_id']);
  DB::transaction(function()use($id,$proposalId,$data){
   $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$data['language_id'])->lockForUpdate()->first();abort_unless($site,404);
   if($site->tenant_id)TenantAccess::require((int)$site->tenant_id);
   $proposal=DB::table('titan_brand_proposals')->where('id',$proposalId)->where('site_id',$id)->where('language_id',$data['language_id'])->lockForUpdate()->first();abort_unless($proposal,404);
   if($proposal->status!=='pending')throw ValidationException::withMessages(['proposal'=>'Proposal already reviewed.']);
   if((int)$site->revision!==(int)$data['revision']||(int)$proposal->source_revision!==(int)$site->revision)throw ValidationException::withMessages(['revision'=>'Draft changed since proposal; generate a fresh proposal.']);
   if($data['decision']==='approve') {
    $doc=SiteDocument::decode($site->draft_document,$site->template_key);
    $changes=json_decode($proposal->changes,true,12,JSON_THROW_ON_ERROR);
    AgentMediaPolicy::requireOwnership($changes,(int)$site->id);
    $updated=BrandProposal::apply($doc,$changes,$site->template_key);
    DB::table('titan_site_revisions')->insert(['site_id'=>$id,'revision'=>$site->revision,'document'=>$site->draft_document,'action'=>'brand-agent','created_at'=>now()]);
    DB::table('titan_sites')->where('id',$id)->update(['draft_document'=>SiteDocument::encode($updated),'revision'=>$site->revision+1,'updated_at'=>now()]);
   }
   DB::table('titan_brand_proposals')->where('id',$proposalId)->update(['status'=>$data['decision']==='approve'?'approved':'rejected','updated_at'=>now()]);
   AgentAudit::write((int)$site->id,(int)$proposalId,'decided',$data['decision'],(int)$site->revision,$site->tenant_id); 
  },3);
  return redirect()->route('admin.sites.brand-agent',['id'=>$id,'language_id'=>$data['language_id']])->with('success','Proposal decision recorded. Publish separately after previewing.');
 }
 private function assertFresh(object $site):void {
  $current=$this->site((int)$site->id,(int)$site->language_id);
  if((int)$current->revision!==(int)$site->revision)throw ValidationException::withMessages(['revision'=>'Draft changed while the agent was working. Refresh and retry.']);
 }
 private function record(object $site,string $kind,array $changes,string $rationale): void {
  AgentMediaPolicy::requireOwnership($changes,(int)$site->id);
  DB::transaction(function()use($site,$kind,$changes,$rationale){
  $proposalId=DB::table('titan_brand_proposals')->insertGetId(['site_id'=>$site->id,'language_id'=>$site->language_id,'source_revision'=>$site->revision,'kind'=>$kind,
   'changes'=>json_encode($changes,JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES),'rationale'=>$rationale,'status'=>'pending','created_at'=>now(),'updated_at'=>now()]);
  AgentAudit::write((int)$site->id,(int)$proposalId,'proposed',$kind,(int)$site->revision,$site->tenant_id);
  });
 }
}
