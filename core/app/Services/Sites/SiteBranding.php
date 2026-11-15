<?php
namespace App\Services\Sites;

use Illuminate\Validation\ValidationException;

/** Whitelisted brand tokens shared by every site renderer and the edit form. */
final class SiteBranding {
    private const FONTS = ['system', 'humanist', 'geometric', 'serif'];
    private const RADIUS = ['square', 'subtle', 'rounded', 'pill'];
    private const DENSITY = ['compact', 'comfortable', 'spacious'];
    private const WIDTH = ['narrow', 'standard', 'wide'];
    public static function defaults(array $document): array {
        return ['primary' => $document['accent'] ?? '#23466f', 'secondary' => '#226a62',
          'surface' => '#ffffff', 'background' => '#f8fafc', 'ink' => '#24313d',
          'font' => 'system', 'radius' => 'rounded', 'density' => 'comfortable', 'width' => 'standard'];
    }
    public static function normalize(array &$document): void {
        $raw = $document['brand'] ?? [];
        if (!is_array($raw) || (array_is_list($raw) && $raw !== []) || count($raw)>9) self::reject();
        $defaults = self::defaults($document);
        foreach ($raw as $key=>$value) {
            if (!array_key_exists($key,$defaults) || !is_string($value)) self::reject();
            if (in_array($key,['primary','secondary','surface','background','ink'],true)) {
                if (!preg_match('/^#[0-9a-fA-F]{6}$/D',$value)) self::reject();
            } else {
                $values = match($key) {
                    'font' => self::FONTS, 'radius' => self::RADIUS,
                    'density' => self::DENSITY, 'width' => self::WIDTH,
                };
                if (!in_array($value,$values,true)) self::reject();
            }
        }
        $document['brand'] = array_merge($defaults,$raw);
        // Legacy accent remains the canonical compatibility colour for old theme references.
        $document['accent'] = $document['brand']['primary'];
    }
    public static function css(array $document): string {
        $values = array_merge(self::defaults($document),$document['brand']??[]);
        $fonts = ['system'=>'system-ui,-apple-system,sans-serif',
          'humanist'=>'"Trebuchet MS",Verdana,sans-serif',
          'geometric'=>'Arial,Helvetica,sans-serif',
          'serif'=>'Georgia,"Times New Roman",serif'];
        $radius = ['square'=>'0px','subtle'=>'5px','rounded'=>'12px','pill'=>'28px'];
        $space = ['compact'=>'0.75','comfortable'=>'1','spacious'=>'1.25'];
        $width = ['narrow'=>'900px','standard'=>'1100px','wide'=>'1320px'];
        return '--site-accent:'.$values['primary'].';--brand-primary:'.$values['primary'].';'.
          '--brand-secondary:'.$values['secondary'].';--brand-surface:'.$values['surface'].';'.
          '--brand-background:'.$values['background'].';--brand-ink:'.$values['ink'].';'.
          '--brand-font:'.$fonts[$values['font']].';--brand-radius:'.$radius[$values['radius']].';'.
          '--brand-spacing:'.$space[$values['density']].';--brand-max-width:'.$width[$values['width']].';';
    }
    private static function reject(): void {
        throw ValidationException::withMessages(['document'=>'Invalid branding settings.']);
    }
}
