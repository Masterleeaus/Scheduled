<?php
namespace App\Services\Sites;
final class SiteTemplates {
    public static function catalog(): array { return [
        'residential'=>['title'=>'Residential cleaning','headline'=>'A fresh home, made simple','tagline'=>'Thoughtful cleaning designed around your life','accent'=>'#23466f'],
        'commercial'=>['title'=>'Commercial cleaning','headline'=>'Workspaces worth walking into','tagline'=>'Reliable commercial care for growing teams','accent'=>'#165a62'],
        'end-of-lease'=>['title'=>'End-of-lease','headline'=>'Move forward with a cleaner slate','tagline'=>'Detailed move-out cleaning from room to room','accent'=>'#765331'],
        'carpet'=>['title'=>'Carpets and upholstery','headline'=>'Restore the comfort of your space','tagline'=>'Specialist textile care for homes and workplaces','accent'=>'#5e4a83'],
        'windows'=>['title'=>'Window cleaning','headline'=>'Let every window shine','tagline'=>'Clearer views, inside and out','accent'=>'#246080'],
        'deep'=>['title'=>'Deep cleaning','headline'=>'Every corner deserves attention','tagline'=>'A detailed clean when ordinary is not enough','accent'=>'#496342'],
        'pressure'=>['title'=>'Pressure washing','headline'=>'Refresh your outdoor spaces','tagline'=>'Outdoor surface care for homes and businesses','accent'=>'#426176'],
        'shortstay'=>['title'=>'Short-stay turnover','headline'=>'Guest-ready from check-out to check-in','tagline'=>'Consistent turnovers for short-stay hosts','accent'=>'#77515d'],
        'office'=>['title'=>'Office janitorial','headline'=>'A cleaner start to every workday','tagline'=>'Dependable office maintenance','accent'=>'#3c596a'],
        'clinar'=>['title'=>'Clinar · Maintenance & Cleaning','headline'=>'Every space deserves exceptional care.','tagline'=>'Professional cleaning and ongoing property care with dependable attention to detail.','accent'=>'#1269a2'],
        'cleanmate'=>['title'=>'CleanMate · Home & Garden','headline'=>'A brighter home starts here.','tagline'=>'Practical cleaning, maid services and garden upkeep for busy households.','accent'=>'#6c982d'],
        'handypro'=>['title'=>'HandyPro · Cleaning Professionals','headline'=>'A polished clean. Every time.','tagline'=>'Professional cleaning for people who value reliability, quality and a personal touch.','accent'=>'#1e9cba'],
        'prinash'=>['title'=>'Prinash · Exterior & Pressure Cleaning','headline'=>'Bring your exterior back to life.','tagline'=>'Considered outdoor washing for driveways, paving and exterior spaces.','accent'=>'#146c89'],
        'klano'=>['title'=>'Klano · Contemporary Cleaning','headline'=>'Clean spaces. Clear minds.','tagline'=>'Fresh, thoughtfully designed cleaning services for home and business.','accent'=>'#e9a33a'],
        'pawfy'=>['title'=>'Pawfy · Pet Care','headline'=>'Care for every companion','tagline'=>'Warm, dependable services for pets and their people','accent'=>'#87a83c'],
        'stevenwatkins'=>['title'=>'Steven Watkins · Personal Training','headline'=>'Build strength on your terms','tagline'=>'Personal training built around your goals','accent'=>'#df7046'],
        'yoga-pilates'=>['title'=>'Yoga & Pilates · Mindful Movement','headline'=>'Move with intention. Feel the difference.','tagline'=>'Grounded yoga and Pilates sessions for all experience levels','accent'=>'#8b6c91'],
        'carzone'=>['title'=>'Carzone · Vehicle Care','headline'=>'Detailing worth driving for','tagline'=>'Thoughtful vehicle care, inside and out','accent'=>'#b82d3d'],
        'multiservice'=>['title'=>'Multi-service','headline'=>'The right clean for every space','tagline'=>'Explore a flexible range of services','accent'=>'#294d63'],
    ]; }
    public static function make(string $key): ?array {
        $definition=self::catalog()[$key]??null;
        if (!$definition) return null;
        $path=__DIR__.'/Presets/'.$key.'.json';
        if (!is_file($path)) throw new \RuntimeException('Missing site template preset: '.$key);
        $contents=file_get_contents($path);
        $doc=json_decode($contents,true);
        if (!is_array($doc)) throw new \RuntimeException('Invalid site template preset: '.$key);
        return \App\Services\Sites\SiteMarketing::normalize($doc);
    }
}
