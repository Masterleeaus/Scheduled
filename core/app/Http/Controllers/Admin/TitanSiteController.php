<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Sites\SiteDocument;
use App\Services\Sites\SiteTemplates;
use App\Services\Sites\TemplateRegistry;
use App\Services\Sites\SiteOnboarding;
use App\Services\Sites\SiteCreationAI;
use App\Services\Sites\BusinessVerticals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\Tenants\TenantAccess;
class TitanSiteController extends Controller {
 private function scoped(int $id,int $language) { $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$language)->first();abort_unless($site,404);if($site->tenant_id)TenantAccess::require((int)$site->tenant_id);return $site; }
 public function index(Request $request) {
  $language=(int)$request->query('language_id',0);abort_unless($language>0 && DB::table('languages')->where('id',$language)->exists(),404);
  $sites=DB::table('titan_sites')->where('language_id',$language)->where(function($q){$q->whereNull('tenant_id')->orWhereIn('tenant_id',TenantAccess::ids());})->orderByDesc('id')->paginate(30);
  return view('admin.sites.index',compact('sites','language'));
 }
 public function create(Request $request) {
  $language=(int)$request->query('language_id',0);abort_unless($language>0 && DB::table('languages')->where('id',$language)->exists(),404);
  $templates=TemplateRegistry::sites();$verticalGroups=BusinessVerticals::groups();$verticalCatalog=BusinessVerticals::all();return view('admin.sites.create',compact('language','templates','verticalGroups','verticalCatalog'));
 }
 public function templatePreview(Request $request,string $key) {
  $language=(int)$request->query('language_id',0);
  abort_unless($language>0 && DB::table('languages')->where('id',$language)->exists(),404);
  $definition=TemplateRegistry::site($key);
  abort_unless($definition && $definition['available'],404);
  $document=SiteDocument::decode(SiteDocument::encode(SiteTemplates::make($key)), $key);
  $site=(object)['name'=>$definition['title'],'language_id'=>$language,'slug'=>'template-preview','template_key'=>$key];
  $preview=true;$placementPicker=$request->boolean('placement_picker');
  return response()->view('front.sites.show',compact('site','document','preview','placementPicker'))
   ->header('Cache-Control','private,no-store')->header('X-Robots-Tag','noindex,nofollow');
 }
 public function creationPreview(Request $request) {
  $data=$request->validate(['tenant_id'=>'nullable|integer|min:1','language_id'=>'required|integer|min:1','name'=>'required|string|max:120','slug'=>'required|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|max:100','template_key'=>'required|string|max:40'] + SiteOnboarding::rules());if(!empty($data['tenant_id']))TenantAccess::require((int)$data['tenant_id']);
  abort_unless(DB::table('languages')->where('id',$data['language_id'])->exists(),404);
  $definition=TemplateRegistry::site($data['template_key']);abort_unless($definition && $definition['available'],404);
  $document=SiteOnboarding::build($data['template_key'],$data);
  if($data['generation_mode']==='assisted')$document=SiteCreationAI::generate($document,$data,$data['template_key']);
  $site=(object)['name'=>$data['name'],'language_id'=>$data['language_id'],'slug'=>$data['slug'],'template_key'=>$data['template_key']];
  $preview=true;$placementPicker=false;
  return response()->view('front.sites.show',compact('site','document','preview','placementPicker'))
   ->header('Cache-Control','private,no-store')->header('X-Robots-Tag','noindex,nofollow');
 }
 public function store(Request $request) {
  $data=$request->validate(['tenant_id'=>'nullable|integer|min:1','language_id'=>'required|integer|min:1','name'=>'required|string|max:120','slug'=>'required|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|max:100','template_key'=>'required|string|max:40'] + SiteOnboarding::rules());if(!empty($data['tenant_id']))TenantAccess::require((int)$data['tenant_id']);
  abort_unless(DB::table('languages')->where('id',$data['language_id'])->exists(),404);
  $definition=TemplateRegistry::site($data['template_key']);
  if(!$definition || !$definition['available'])throw ValidationException::withMessages(['template_key'=>'Template assets are unavailable.']);
  $doc=SiteOnboarding::build($data['template_key'],$data);
  if($data['generation_mode']==='assisted')$doc=SiteCreationAI::generate($doc,$data,$data['template_key']);
  if(DB::table('titan_sites')->where('language_id',$data['language_id'])->where('slug',$data['slug'])->exists())
   throw ValidationException::withMessages(['slug'=>'Slug already in use for this site language.']);
  $id=DB::table('titan_sites')->insertGetId(['language_id'=>$data['language_id'],'tenant_id'=>$data['tenant_id']??null,'name'=>$data['name'],'slug'=>$data['slug'],'template_key'=>$data['template_key'],
   'draft_document'=>SiteDocument::encode($doc),'revision'=>1,'published'=>false,'created_at'=>now(),'updated_at'=>now()]);
  return redirect()->route('admin.sites.edit',['id'=>$id,'language_id'=>$data['language_id']]);
 }
 public function duplicate(Request $request,int $id) {
  $data=$request->validate(['language_id'=>'required|integer|min:1','name'=>'required|string|min:2|max:120','slug'=>'required|string|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|max:100']);
  $source=$this->scoped($id,(int)$data['language_id']);
  $newId=DB::transaction(function()use($source,$data){
   if(DB::table('titan_sites')->where('language_id',$source->language_id)->where('slug',$data['slug'])->exists())throw ValidationException::withMessages(['slug'=>'Slug already in use.']);
   return DB::table('titan_sites')->insertGetId(['language_id'=>$source->language_id,'name'=>$data['name'],'slug'=>$data['slug'],
    'template_key'=>$source->template_key,'tenant_id'=>$source->tenant_id,'draft_document'=>$source->draft_document,'revision'=>1,'published'=>false,
    'published_document'=>null,'published_at'=>null,'created_at'=>now(),'updated_at'=>now()]);
  },3);
  return redirect()->route('admin.sites.edit',['id'=>$newId,'language_id'=>$source->language_id])->with('success','Independent unpublished site created.');
 }
 public function edit(Request $request,int $id) {
  $language=(int)$request->query('language_id',0);$site=$this->scoped($id,$language);
  return view('admin.sites.edit',['site'=>$site,'language'=>$language,'document'=>json_decode($site->draft_document,true)]);
 }
 public function update(Request $request,int $id) {
  $data=$request->validate(SiteDocument::rules());
  $current=$this->scoped($id,(int)$data['language_id']);
  $doc=SiteDocument::decode($data['document'],$current->template_key);
  DB::transaction(function() use($id,$data,$doc) {
   $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$data['language_id'])->lockForUpdate()->first();abort_unless($site,404);if($site->tenant_id)TenantAccess::require((int)$site->tenant_id);
   if((int)$site->revision!==(int)$data['revision'])throw ValidationException::withMessages(['revision'=>'Stale editor; reload before saving.']);
   if(DB::table('titan_sites')->where('language_id',$site->language_id)->where('slug',$data['slug'])->where('id','!=',$id)->exists())throw ValidationException::withMessages(['slug'=>'Slug already in use.']);
   DB::table('titan_site_revisions')->insert(['site_id'=>$id,'revision'=>$site->revision,'document'=>$site->draft_document,'action'=>'edit','created_at'=>now()]);
   DB::table('titan_sites')->where('id',$id)->update(['name'=>$data['name'],'slug'=>$data['slug'],'draft_document'=>SiteDocument::encode($doc),'revision'=>$site->revision+1,'updated_at'=>now()]);
  },3);
  return redirect()->route('admin.sites.edit',['id'=>$id,'language_id'=>$data['language_id']])->with('success','Draft saved.');
 }
 public function publish(Request $request,int $id) {
  $data=$request->validate(['language_id'=>'required|integer|min:1','revision'=>'required|integer|min:1','publish'=>'required|boolean']);
  DB::transaction(function()use($id,$data){
   $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$data['language_id'])->lockForUpdate()->first();abort_unless($site,404);if($site->tenant_id)TenantAccess::require((int)$site->tenant_id);
   if((int)$site->revision!==(int)$data['revision'])throw ValidationException::withMessages(['revision'=>'Stale editor; reload.']);
   // Publish is a snapshot; later draft edits never change the public page.
   $changes=['published'=>(bool)$data['publish'],'revision'=>$site->revision+1,'updated_at'=>now()];
   if($data['publish']){$changes['published_document']=$site->draft_document;$changes['published_at']=now();}
   DB::table('titan_site_revisions')->insert(['site_id'=>$id,'revision'=>$site->revision,'document'=>$site->draft_document,'action'=>$data['publish']?'publish':'unpublish','created_at'=>now()]);
   DB::table('titan_sites')->where('id',$id)->update($changes);
  },3);
  return redirect()->route('admin.sites.edit',['id'=>$id,'language_id'=>$data['language_id']])->with('success','Publication updated.');
 }
 public function restore(Request $request,int $id) {
  $data=$request->validate(['language_id'=>'required|integer|min:1','revision'=>'required|integer|min:1','restore_revision'=>'required|integer|min:1']);
  DB::transaction(function()use($id,$data){
   $site=DB::table('titan_sites')->where('id',$id)->where('language_id',$data['language_id'])->lockForUpdate()->first();abort_unless($site,404);if($site->tenant_id)TenantAccess::require((int)$site->tenant_id);
   if((int)$site->revision!==(int)$data['revision'])throw ValidationException::withMessages(['revision'=>'Stale editor; reload.']);
   $prior=DB::table('titan_site_revisions')->where('site_id',$id)->where('revision',$data['restore_revision'])->first();abort_unless($prior,404);
   DB::table('titan_site_revisions')->insert(['site_id'=>$id,'revision'=>$site->revision,'document'=>$site->draft_document,'action'=>'restore','created_at'=>now()]);
   DB::table('titan_sites')->where('id',$id)->update(['draft_document'=>$prior->document,'revision'=>$site->revision+1,'updated_at'=>now()]);
  },3);
  return redirect()->route('admin.sites.edit',['id'=>$id,'language_id'=>$data['language_id']])->with('success','Draft restored; publish separately.');
 }
 public function history(Request $request,int $id) {
  $language=(int)$request->query('language_id',0);$site=$this->scoped($id,$language);
  $revisions=DB::table('titan_site_revisions')->where('site_id',$site->id)->orderByDesc('revision')->limit(40)->get();
  return view('admin.sites.history',compact('site','language','revisions'));
 }
 public function leads(Request $request,int $id) {
  $language=(int)$request->query('language_id',0);$site=$this->scoped($id,$language);
  $leads=DB::table('titan_site_leads')->where('site_id',$site->id)->where('language_id',$language)->orderByDesc('id')->paginate(30);
  return response()->view('admin.sites.leads',compact('site','language','leads'))->header('Cache-Control','private,no-store');
 }

 public function uploadImage(Request $request,int $id) {
  $language=(int)$request->input('language_id',0);
  $site=$this->scoped($id,$language);
  $request->validate(['language_id'=>'required|integer|min:1','image'=>'required|file|image|mimes:jpeg,png,webp,gif|max:5120']);
  $file=$request->file('image');
  if (!$file || !$file->isValid()) throw ValidationException::withMessages(['image'=>'Upload failed.']);
  $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());
  $extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
  if (!isset($extensions[$mime]) || @getimagesize($file->getRealPath())===false) throw ValidationException::withMessages(['image'=>'Invalid image content.']);
  $directory=public_path('uploads/site-media/'.(int)$site->id);
  if (!is_dir($directory) && !mkdir($directory,0755,true) && !is_dir($directory)) abort(500,'Media storage unavailable.');
  $filename=bin2hex(random_bytes(20)).'.'.$extensions[$mime];
  $file->move($directory,$filename);
  return response()->json(['url'=>asset('uploads/site-media/'.(int)$site->id.'/'.$filename),'name'=>$file->getClientOriginalName()],201)
    ->header('Cache-Control','private,no-store')->header('X-Content-Type-Options','nosniff');
 }

 public function preview(Request $request,int $id) {
  $language=(int)$request->query('language_id',0);$site=$this->scoped($id,$language);
  $document=json_decode($site->draft_document,true);abort_unless(is_array($document),404);$preview=true;$placementPicker=$request->boolean('placement_picker');
  return response()->view('front.sites.show',compact('site','document','preview','placementPicker'))->header('Cache-Control','private,no-store')->header('X-Robots-Tag','noindex,nofollow');
 }
}
