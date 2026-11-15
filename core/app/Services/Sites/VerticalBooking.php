<?php
namespace App\Services\Sites;
/** Presentation only: a request is NOT a confirmed Cleanto reservation. */
final class VerticalBooking {
 public const THEMES = ['pawfy','stevenwatkins','yoga-pilates','carzone'];
 public static function enabled(string $template): bool { return in_array($template,self::THEMES,true); }
 public static function presentation(string $template,?string $vertical): array {
  $group = match($template) {'pawfy'=>'pet','stevenwatkins'=>'fitness','yoga-pilates'=>'yoga','carzone'=>'vehicle',default=>'general'};
  // Use one validated data model for all themes; only labels differ.
  return match($group) {
   'pet'=>['title'=>'Request a pet-care appointment','intro'=>'Choose a pet service and preferred time. We will confirm availability with you.','subject'=>'Pet name','subject_hint'=>'Pet name (optional)','notes'=>'Tell us about your pet, care needs and any important handling information.','action'=>'Send pet-care request','detail_key'=>'pet_name'],
   'fitness'=>['title'=>'Request a personal-training session','intro'=>'Tell us which session suits you and your preferred time. We will confirm before booking.','subject'=>'Training focus','subject_hint'=>'e.g. Strength or mobility (optional)','notes'=>'Share your training goals and preferred location or format.','action'=>'Request a training session','detail_key'=>'training_focus'],
   'yoga'=>['title'=>'Request a yoga or Pilates session','intro'=>'Choose a class or session and a preferred time. We will confirm places and arrangements.','subject'=>'Class preference','subject_hint'=>'e.g. Beginner yoga or reformer Pilates (optional)','notes'=>'Tell us about your experience level and any accessibility requirements you want us to know.','action'=>'Request a class or session','detail_key'=>'class_preference'],
   'vehicle'=>['title'=>'Request vehicle detailing','intro'=>'Choose the service and preferred appointment time. We will confirm the price and availability.','subject'=>'Vehicle details','subject_hint'=>'Make and model (optional)','notes'=>'Tell us about your vehicle, condition, preferred service and location.','action'=>'Request vehicle service','detail_key'=>'vehicle_details'],
   default=>['title'=>'Request an appointment','intro'=>'Tell us your preferred time. We will confirm availability.','subject'=>'Details','subject_hint'=>'Optional','notes'=>'Additional details','action'=>'Send request','detail_key'=>'subject']
  };
 }
 public static function details(array $data): string {
   return json_encode(['preferred_date'=>$data['preferred_date']??null,'preferred_time'=>$data['preferred_time']??null,'subject'=>$data['booking_subject']??null], JSON_THROW_ON_ERROR);
 }
 public static function validateAdditional(array $input): array {
  return \Illuminate\Support\Facades\Validator::make($input,[
   'preferred_date'=>'required|date_format:Y-m-d|after_or_equal:today',
   'preferred_time'=>'required|date_format:H:i',
   'booking_subject'=>'nullable|string|max:160',
  ])->validate();
 }
}
