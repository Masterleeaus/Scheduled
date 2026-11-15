<?php
namespace App\Services\Landing;
final class CampaignTemplates {
 public static function definitions(): array { return [
  'home-cleaning'=>['title'=>'Home cleaning','headline'=>'A beautifully clean home, without the hassle','subheading'=>'Flexible home cleaning tailored to your schedule.','cta'=>'Request a cleaning quote'],
  'commercial'=>['title'=>'Commercial cleaning','headline'=>'A cleaner workplace starts here','subheading'=>'Reliable cleaning for offices and workplaces.','cta'=>'Request a business quote'],
  'end-of-lease'=>['title'=>'End-of-lease','headline'=>'Move out with confidence','subheading'=>'Detailed end-of-lease cleaning for your next move.','cta'=>'Get a move-out quote'],
  'carpet'=>['title'=>'Carpet care','headline'=>'Fresh carpets, refreshed spaces','subheading'=>'Professional carpet and upholstery care.','cta'=>'Ask for a quote'],
  'window'=>['title'=>'Window cleaning','headline'=>'Let the light back in','subheading'=>'Streak-free windows for home and business.','cta'=>'Request window cleaning'],
  'deep-clean'=>['title'=>'Deep cleaning','headline'=>'A deeper kind of clean','subheading'=>'Attention to detail for kitchens, bathrooms and more.','cta'=>'Request deep cleaning'],
  'pressure'=>['title'=>'Pressure washing','headline'=>'Bring outdoor surfaces back to life','subheading'=>'Exterior surface cleaning for paths and patios.','cta'=>'Get an exterior quote'],
  'short-stay'=>['title'=>'Short-stay turnover','headline'=>'Guest-ready, every time','subheading'=>'Reliable turnovers for short-stay properties.','cta'=>'Ask about turnovers'],
  'office'=>['title'=>'Office janitorial','headline'=>'Professional offices, consistently clean','subheading'=>'A dependable cleaning plan for your team.','cta'=>'Discuss office cleaning'],
  'multi-service'=>['title'=>'All cleaning services','headline'=>'One team for all your cleaning needs','subheading'=>'Explore a tailored plan for your property.','cta'=>'Request a free quote'],
 ]; }
 public static function get(string $key): ?array { return self::definitions()[$key] ?? null; }
}
