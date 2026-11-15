<?php
namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Tenants\DomainBinding;
use App\Services\Landing\PublishedCampaign;
use Illuminate\Support\Facades\DB;
class LandingCampaignController extends Controller {
 public function show(string $slug) {
  $campaign=DB::table('titan_landing_campaigns')->where('published_slug',$slug)->where('published',1)->first();abort_unless($campaign,404);DomainBinding::assertPublicHost($campaign->tenant_id??null,'campaign',(int)$campaign->id,request()->getHost());
  $campaign=PublishedCampaign::render($campaign);$preview=false;return response()->view('front.landing.show',compact('campaign','preview'))->header('Cache-Control','public, max-age=60');
 }
 public function submit(Request $request,string $slug) {
  $campaign=DB::table('titan_landing_campaigns')->where('published_slug',$slug)->where('published',1)->first();abort_unless($campaign,404);DomainBinding::assertPublicHost($campaign->tenant_id??null,'campaign',(int)$campaign->id,request()->getHost());
  $v=$request->validate(['name'=>'required|string|max:120','email'=>'required|email|max:254','phone'=>'nullable|string|max:40','message'=>'nullable|string|max:2000',
   'consent'=>'accepted','website'=>'nullable|string|max:200','utm_source'=>'nullable|string|max:120','utm_medium'=>'nullable|string|max:120','utm_campaign'=>'nullable|string|max:120']);
  // Honeypot: acknowledge without storing spam or revealing the filter.
  if(!empty($v['website'])) return redirect()->route('front.landing.show',$slug)->with('landing_received',true);
  $fields=array_intersect_key($v,array_flip(['name','email','phone','message','utm_source','utm_medium','utm_campaign']));
  $fields['email']=strtolower($fields['email']);
  $fields['campaign_id']=$campaign->id;$fields['language_id']=$campaign->language_id;$fields['consent']=true;$fields['created_at']=now();$fields['updated_at']=now();
  DB::table('titan_landing_leads')->insert($fields);
  return redirect()->route('front.landing.show',$slug)->with('landing_received',true);
 }
}
