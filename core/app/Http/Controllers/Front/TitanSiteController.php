<?php
namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Tenants\DomainBinding;
use App\Services\Sites\SiteLeadValidator;
class TitanSiteController extends Controller {
 public function show(int $language,string $slug) {
  $site=DB::table('titan_sites')->where('language_id',$language)->where('slug',$slug)->where('published',true)->first();
  abort_unless($site && $site->published_document,404);DomainBinding::assertPublicHost($site->tenant_id??null,'site',(int)$site->id,request()->getHost());
  $document=json_decode($site->published_document,true);abort_unless(is_array($document),404);
  $preview=false;return response()->view('front.sites.show',compact('site','document','preview'));
 }
 public function submit(Request $request,int $language,string $slug) {
  $data=$request->validate(SiteLeadValidator::rules());
  $site=DB::table('titan_sites')->where('language_id',$language)->where('slug',$slug)->where('published',true)->first();
  abort_unless($site && $site->published_document,404);DomainBinding::assertPublicHost($site->tenant_id??null,'site',(int)$site->id,request()->getHost());
  $document=json_decode($site->published_document,true);abort_unless(is_array($document),404);
  // Acknowledge honeypot submissions without storing the lead or revealing the filter.
  if (!empty($data['website'])) {
   return redirect()->route('front.sites.show',['language'=>$language,'slug'=>$slug])->with('site_lead_success','Your enquiry has been received.');
  }
  SiteLeadValidator::check($data,$document);
  $bookingRequest = \App\Services\Sites\VerticalBooking::enabled((string)$site->template_key);
  $bookingData = $bookingRequest ? \App\Services\Sites\VerticalBooking::validateAdditional($request->all()) : [];
  if ($bookingRequest && (strtotime($bookingData['preferred_date']) > strtotime('+366 days'))) {
   throw \Illuminate\Validation\ValidationException::withMessages(['preferred_date'=>'Choose a date within the next year.']);
  }
  DB::table('titan_site_leads')->insert(array_merge(['site_id'=>$site->id,'language_id'=>$site->language_id,'name'=>$data['name'],
   'email'=>strtolower($data['email']),'phone'=>$data['phone']??null,'service'=>$data['service']??null,'utm_source'=>$data['utm_source']??null,'utm_medium'=>$data['utm_medium']??null,'utm_campaign'=>$data['utm_campaign']??null,'promotion_code'=>$data['promotion_code']??null,'message'=>$data['message'],
   'consented_at'=>now(),'created_at'=>now(),'updated_at'=>now()], $bookingRequest ? [
   'request_kind'=>'appointment_request',
   'preferred_date'=>$bookingData['preferred_date'],
   'preferred_time'=>$bookingData['preferred_time'].':00',
   'booking_details'=>\App\Services\Sites\VerticalBooking::details($bookingData)
  ] : []));
  return redirect()->route('front.sites.show',['language'=>$language,'slug'=>$slug])->with('site_lead_success',$bookingRequest ? 'Your appointment request has been received. We will contact you to confirm it.' : 'Your enquiry has been received.');
 }
}
