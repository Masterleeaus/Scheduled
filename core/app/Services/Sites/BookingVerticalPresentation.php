<?php
/** Industry terminology is presentation-only: pricing, duration and slots remain Cleanto authoritative. */
declare(strict_types=1);
namespace App\Services\Sites;
final class BookingVerticalPresentation {
    private const GROUPS = [
      'Cleaning services'=>['service'=>'Cleaning service','staff'=>'Cleaning professional','units'=>'Number of units','address'=>'Service address','title'=>'Check cleaning availability','schedule'=>'Appointment'],
      'Vehicles, appliances and furniture'=>['service'=>'Vehicle or restoration service','staff'=>'Service professional','units'=>'Number of items','address'=>'Service location','title'=>'Check service availability','schedule'=>'Appointment'],
      'Trades and property maintenance'=>['service'=>'Property service','staff'=>'Trade professional','units'=>'Number of items or areas','address'=>'Job site address','title'=>'Check trade availability','schedule'=>'Site visit'],
      'Outdoor and garden'=>['service'=>'Garden or outdoor service','staff'=>'Outdoor services professional','units'=>'Number of areas','address'=>'Service address','title'=>'Check outdoor service availability','schedule'=>'Site visit'],
      'Pet services'=>['service'=>'Pet-care service','staff'=>'Pet-care professional','units'=>'Number of pets','address'=>'Care location','title'=>'Check pet-care availability','schedule'=>'Appointment'],
      'Personal services and beauty'=>['service'=>'Beauty or personal service','staff'=>'Service professional','units'=>'Number of clients','address'=>'Appointment location','title'=>'Check appointment availability','schedule'=>'Appointment'],
      'Health, fitness and wellbeing'=>['service'=>'Fitness or wellbeing session','staff'=>'Instructor or consultant','units'=>'Number of participants','address'=>'Session location','title'=>'Check session availability','schedule'=>'Session'],
      'Education'=>['service'=>'Tutoring session','staff'=>'Tutor','units'=>'Number of students','address'=>'Session location','title'=>'Check tutoring availability','schedule'=>'Lesson'],
      'Property and hospitality'=>['service'=>'Property management service','staff'=>'Property professional','units'=>'Number of properties','address'=>'Property address','title'=>'Check appointment availability','schedule'=>'Consultation'],
      'Specialist'=>['service'=>'Specialist service','staff'=>'Qualified professional','units'=>'Number of areas','address'=>'Site address','title'=>'Request a specialist assessment','schedule'=>'Assessment'],
    ];
    private const OVERRIDES = [
      'dog-walking'=>['service'=>'Dog walking','units'=>'Number of dogs','schedule'=>'Walk'],
      'pet-and-house-sitting'=>['service'=>'Pet and house sitting','units'=>'Number of pets','schedule'=>'Care visit'],
      'pet-grooming'=>['service'=>'Pet grooming','units'=>'Number of pets','schedule'=>'Grooming appointment'],
      'dog-training'=>['service'=>'Dog training','units'=>'Number of dogs','schedule'=>'Training lesson'],
      'car-detailing'=>['service'=>'Vehicle detailing','units'=>'Number of vehicles','address'=>'Vehicle location','schedule'=>'Detailing appointment'],
      'personal-and-fitness-training'=>['service'=>'Personal training','staff'=>'Personal trainer','units'=>'Number of participants','schedule'=>'Training session'],
      'yoga-and-pilates'=>['service'=>'Yoga or Pilates class','staff'=>'Instructor','units'=>'Number of participants','schedule'=>'Class'],
      'nutrition-weight-loss-and-dieting-consultant'=>['service'=>'Nutrition consultation','staff'=>'Consultant','units'=>'Number of clients','schedule'=>'Consultation'],
      'tutoring'=>['service'=>'Tutoring lesson','staff'=>'Tutor','units'=>'Number of students','schedule'=>'Lesson'],
      'short-stay-host-management'=>['service'=>'Short-stay hosting service','units'=>'Number of properties','schedule'=>'Consultation'],
      'pool-and-spa-servicing'=>['service'=>'Pool or spa service','units'=>'Number of pools or spas'],
      'aquarium-maintenance'=>['service'=>'Aquarium maintenance','units'=>'Number of aquariums'],
      'mobile-hairdressing-and-barbering'=>['service'=>'Hairdressing or barbering','units'=>'Number of clients'],
      'mobile-beauty'=>['service'=>'Mobile beauty service','units'=>'Number of clients'],
      'mobile-massage'=>['service'=>'Massage session','units'=>'Number of clients'],
      'makeup-artistry'=>['service'=>'Makeup session','units'=>'Number of clients'],
      'mobile-nail-technology'=>['service'=>'Nail appointment','units'=>'Number of clients'],
      'high-risk-cleaning'=>['title'=>'Request a specialist assessment','service'=>'Specialist cleaning assessment','schedule'=>'Assessment'],
    ];
    public static function forVertical(?string $verticalKey, string $theme): array {
       $row=BusinessVerticals::find($verticalKey);
       $group=$row['group']??null;
       $base=self::GROUPS[$group]??self::GROUPS['Cleaning services'];
       if($row) $base=array_replace($base,self::OVERRIDES[$row['key']]??[]);
       // Fallback for older imported themes without a vertical key.
       if(!$row){
          $legacy=[
           'pawfy'=>'Pet services','stevenwatkins'=>'Health, fitness and wellbeing',
           'yoga-pilates'=>'Health, fitness and wellbeing','carzone'=>'Vehicles, appliances and furniture'
          ];
          $base=self::GROUPS[$legacy[$theme]??'Cleaning services'];
          $base=array_replace($base,self::OVERRIDES[match($theme){'pawfy'=>'pet-grooming','stevenwatkins'=>'personal-and-fitness-training','yoga-pilates'=>'yoga-and-pilates','carzone'=>'car-detailing',default=>''}]??[]);
       }
       return $base+['pricing'=>'Pricing method','size'=>'Service option','duration'=>'Duration (minutes)',
           'staff_prompt'=>'Choose a professional','service_prompt'=>'Choose a service'];
    }
    public static function supportsAllCatalogueGroups():bool {
       foreach(BusinessVerticals::all() as $row) if(!isset(self::GROUPS[$row['group']]))return false;
       return true;
    }
}
