<?php
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Booking\{BookingOwnerGateway,BookingOwnerRequest,BookingOwnerMutationRequest};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
/** All routes belong to the authenticated PlusAgency business user group. */
final class BookingOwnerController extends Controller {
 private function invoke(BookingOwnerGateway $gateway,string $operation,array $data){
  try {return response()->json($gateway->call((int)Auth::id(),$operation,$data));}
  catch(\InvalidArgumentException $e){return response()->json(['error'=>'Invalid booking details'],422);}
  catch(\DomainException $e){return response()->json(['error'=>'Booking rule rejected request'],409);}
  catch(\Throwable $e){report($e);return response()->json(['error'=>'Booking service unavailable'],503);}
 }
 public function index(){return view('user.booking-management');}
 private function filters(Request $r):array {return $r->only(['from','to','status','staff_id']);}
 public function list(Request $r,BookingOwnerGateway $g){return $this->invoke($g,'owner_booking_list',array_merge($this->filters($r),['limit'=>$r->input('limit',30),'offset'=>$r->input('offset',0)]));}
 public function summary(Request $r,BookingOwnerGateway $g){return $this->invoke($g,'owner_booking_summary',$this->filters($r));}
 /** Download a bounded export. The gateway, not request input, determines tenant. */
 public function export(Request $r,BookingOwnerGateway $g){
  try {
   $filter=$this->filters($r);$records=[];
   for($offset=0;$offset<1000;$offset+=100){
    $page=$g->call((int)Auth::id(),'owner_booking_list',array_merge($filter,['limit'=>100,'offset'=>$offset]));
    $batch=$page['bookings']??[];
    if(!is_array($batch))throw new \RuntimeException('Invalid booking list response');
    foreach($batch as $record)$records[]=$record;
    if(count($batch)<100)break;
   }
   return response()->streamDownload(function() use ($records){
    $fp=fopen('php://output','w');fputcsv($fp,['Booking ID','Order ID','Date','Status','Customer','Email','Phone','Staff IDs','Service ID']);
    foreach($records as $record){
     $values=[];foreach(['id','order_id','booking_date_time','booking_status','client_name','client_email','client_phone','staff_ids','service_id'] as $key){
      $value=str_replace(["\r","\n"],' ',(string)($record[$key]??''));
      // Prevent spreadsheet formula execution in exported customer-supplied fields.
      if(preg_match('/^\s*[=+@\-]/u',$value))$value="'".$value;
      $values[]=$value;
     }fputcsv($fp,$values);
    }fclose($fp);
   },'bookings-'.date('Ymd-His').'.csv',['Content-Type'=>'text/csv; charset=UTF-8','Cache-Control'=>'no-store']);
  }catch(\InvalidArgumentException $e){return response()->json(['error'=>'Invalid booking filters'],422);}
  catch(\Throwable $e){report($e);return response()->json(['error'=>'Booking export unavailable'],503);}
 }

 public function show(Request $r,BookingOwnerGateway $g,int $id){return $this->invoke($g,'owner_booking_get',['booking_id'=>$id]);}
 public function create(Request $r,BookingOwnerGateway $g){try{$p=BookingOwnerRequest::create($r->all());}catch(\Throwable $e){return response()->json(['error'=>'Invalid booking input'],422);}return $this->invoke($g,'owner_booking_create',$p);}
 public function cancel(Request $r,BookingOwnerGateway $g,int $id){try{$p=BookingOwnerMutationRequest::cancel(array_merge($r->all(),['booking_id'=>$id]));}catch(\Throwable $e){return response()->json(['error'=>'Invalid cancellation input'],422);}return $this->invoke($g,'owner_booking_cancel',$p);}
 public function reschedule(Request $r,BookingOwnerGateway $g,int $id){try{$p=BookingOwnerMutationRequest::reschedule(array_merge($r->all(),['booking_id'=>$id]));}catch(\Throwable $e){return response()->json(['error'=>'Invalid rescheduling input'],422);}return $this->invoke($g,'owner_booking_reschedule',$p);}
}
