<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Landing\CampaignInput;
use App\Services\Landing\CampaignDesign;
use App\Services\Landing\PublishedCampaign;
use App\Services\Landing\CampaignTemplates;
use App\Services\Sites\TemplateRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\Tenants\TenantAccess;
class LandingCampaignController extends Controller {
 public function index() { $campaigns=DB::table('titan_landing_campaigns')->where(function($q){$q->whereNull('tenant_id')->orWhereIn('tenant_id',TenantAccess::ids());})->orderByDesc('id')->paginate(30); return view('admin.landing.index',compact('campaigns')); }
 public function create() { $templates=TemplateRegistry::campaigns(); $languages=DB::table('languages')->select('id','name','code')->get(); return view('admin.landing.edit',['campaign'=>null,'templates'=>$templates,'languages'=>$languages,'design'=>CampaignDesign::defaults(array_key_first($templates))]); }
 public function store(Request $request) {
  $valid=$request->validate(CampaignInput::rules());
  if(!TemplateRegistry::campaign($valid['template']))throw ValidationException::withMessages(['template'=>'Unknown campaign template.']);
  $data=CampaignInput::fields($valid);if($request->filled('tenant_id')){ $tenant=(int)$request->input('tenant_id');TenantAccess::require($tenant);$data['tenant_id']=$tenant;} $data['created_at']=now(); $data['updated_at']=now();
  if(DB::table('titan_landing_campaigns')->where('slug',$data['slug'])->exists()) throw ValidationException::withMessages(['slug'=>'Slug already in use.']);
  $id=DB::table('titan_landing_campaigns')->insertGetId($data);
  return redirect()->route('admin.landing.edit',$id)->with('success','Campaign draft created.');
 }
 public function duplicate(Request $request,$id) {
  $data=$request->validate(['name'=>'required|string|min:2|max:120','slug'=>'required|string|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|max:100']);
  $idNew=DB::transaction(function()use($id,$data){
   $original=DB::table('titan_landing_campaigns')->where('id',$id)->first();abort_unless($original,404);if($original->tenant_id)TenantAccess::require((int)$original->tenant_id);
   if(DB::table('titan_landing_campaigns')->where('slug',$data['slug'])->exists())throw ValidationException::withMessages(['slug'=>'Slug already in use.']);
   $copy=(array)$original;unset($copy['id']);$copy['name']=$data['name'];$copy['slug']=$data['slug'];
   $copy['revision']=1;$copy['published']=false;$copy['published_at']=null;$copy['published_slug']=null;$copy['published_snapshot']=null;$copy['created_at']=now();$copy['updated_at']=now();
   return DB::table('titan_landing_campaigns')->insertGetId($copy);
  },3);
  return redirect()->route('admin.landing.edit',$idNew)->with('success','Independent unpublished campaign created.');
 }
 public function edit($id) { $campaign=DB::table('titan_landing_campaigns')->where('id',$id)->first(); abort_unless($campaign,404);if($campaign->tenant_id)TenantAccess::require((int)$campaign->tenant_id); $templates=TemplateRegistry::campaigns(); $languages=DB::table('languages')->select('id','name','code')->get();  $design=CampaignDesign::decode($campaign->design_json??null,$campaign->template); return view('admin.landing.edit',compact('campaign','templates','languages','design'));  }
 public function update(Request $request,$id) {
  $valid=$request->validate(CampaignInput::rules());
  DB::transaction(function() use($id,$valid) {
   $row=DB::table('titan_landing_campaigns')->where('id',$id)->lockForUpdate()->first(); abort_unless($row,404);if($row->tenant_id)TenantAccess::require((int)$row->tenant_id);
   if((int)($valid['revision']??0)!==(int)$row->revision) throw ValidationException::withMessages(['revision'=>'Campaign changed elsewhere. Reload before editing.']);
   $data=CampaignInput::fields($valid);
   if(DB::table('titan_landing_campaigns')->where('slug',$data['slug'])->where('id','!=',$id)->exists()) throw ValidationException::withMessages(['slug'=>'Slug already in use.']);
   if(DB::table('titan_landing_campaigns')->where('published_slug',$data['slug'])->where('id','!=',$id)->where('published',1)->exists())throw ValidationException::withMessages(['slug'=>'Slug belongs to another published campaign.']);
   $data['revision']=$row->revision+1;$data['updated_at']=now();
   DB::table('titan_landing_campaigns')->where('id',$id)->update($data);
  },3);
  return redirect()->route('admin.landing.edit',$id)->with('success','Campaign saved.');
 }
 public function publish(Request $request,$id) {
  $valid=$request->validate(['revision'=>'required|integer|min:1','published'=>'required|boolean']);
  DB::transaction(function() use($id,$valid) {
   $row=DB::table('titan_landing_campaigns')->where('id',$id)->lockForUpdate()->first(); abort_unless($row,404);if($row->tenant_id)TenantAccess::require((int)$row->tenant_id);
   if((int)$valid['revision']!==(int)$row->revision) throw ValidationException::withMessages(['revision'=>'Campaign changed; reload before publishing.']);
   $patch=['published'=>(bool)$valid['published'],'published_at'=>$valid['published']?now():null,'revision'=>$row->revision+1,'updated_at'=>now()];
   if($valid['published']) {
    if(DB::table('titan_landing_campaigns')->where('published_slug',$row->slug)->where('id','!=',$id)->exists())throw ValidationException::withMessages(['slug'=>'Public slug is already in use by a published campaign.']);
    $patch['published_slug']=$row->slug;
    $patch['published_snapshot']=PublishedCampaign::snapshot($row);
   }
   DB::table('titan_landing_campaigns')->where('id',$id)->update($patch);
  },3);
  return redirect()->route('admin.landing.edit',$id)->with('success','Publication state updated.');
 }
 public function leads($id) {
  $campaign=DB::table('titan_landing_campaigns')->where('id',$id)->first();abort_unless($campaign,404);if($campaign->tenant_id)TenantAccess::require((int)$campaign->tenant_id);
  $leads=DB::table('titan_landing_leads')->where('campaign_id',$id)->orderByDesc('id')->paginate(50);
  return response()->view('admin.landing.leads',compact('campaign','leads'))->header('Cache-Control','private, no-store');
 }
 public function uploadImage(Request $request,$id) {
  $campaign=DB::table('titan_landing_campaigns')->where('id',$id)->first();abort_unless($campaign,404);
  if($campaign->tenant_id)TenantAccess::require((int)$campaign->tenant_id);
  $request->validate(['image'=>'required|file|image|mimes:jpeg,png,webp,gif|max:5120']);
  $file=$request->file('image');
  if(!$file || !$file->isValid())throw ValidationException::withMessages(['image'=>'Image upload failed.']);
  $mime=(new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());
  $extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
  if(!isset($extensions[$mime]) || @getimagesize($file->getRealPath())===false)throw ValidationException::withMessages(['image'=>'Invalid image content.']);
  $directory=public_path('uploads/landing-media/'.(int)$campaign->id);
  if(!is_dir($directory)&&!mkdir($directory,0755,true)&&!is_dir($directory))abort(500,'Image storage unavailable.');
  $filename=bin2hex(random_bytes(20)).'.'.$extensions[$mime];
  $file->move($directory,$filename);
  return response()->json(['url'=>'/uploads/landing-media/'.(int)$campaign->id.'/'.$filename],201)
   ->header('Cache-Control','private,no-store')->header('X-Content-Type-Options','nosniff');
 }
 public function preview($id) { $campaign=DB::table('titan_landing_campaigns')->where('id',$id)->first();abort_unless($campaign,404);if($campaign->tenant_id)TenantAccess::require((int)$campaign->tenant_id);$preview=true;return response()->view('front.landing.show',compact('campaign','preview'))->header('Cache-Control','private, no-store')->header('X-Robots-Tag','noindex, nofollow'); }
}
