<?php
namespace App\Services\Sites;

use Illuminate\Validation\ValidationException;

/** Site owned media references and deterministic DOM placement; no user HTML is rendered. */
final class SiteImages {
    public static function allowedUrl(string $url): bool {
        return \App\Services\Sites\ThemeFields::safeImage($url);
    }
    public static function validateBlocks($blocks): array {
        if (!is_array($blocks) || (array_is_list($blocks) === false && $blocks !== []) || count($blocks)>60) self::fail();
        $seen=[];
        foreach($blocks as $block) {
            if (!is_array($block) || array_keys($block)!==['id','url','alt','selector','index','position','width']) self::fail();
            foreach(['id','url','alt','selector','position','width'] as $k) if (!is_string($block[$k])) self::fail();
            if (!preg_match('/^[a-z0-9-]{6,60}$/D',$block['id']) || isset($seen[$block['id']])) self::fail();
            $seen[$block['id']]=true;
            if (strlen($block['url'])>500 || !self::allowedUrl($block['url'])) self::fail();
            if (SiteDocument::characterLength($block['alt'])>180) self::fail();
            // Generated selectors are CSS element paths: restrict to safe tags, classes, IDs and nth-of-type.
            if (strlen($block['selector'])>350 || !preg_match('/^[a-zA-Z0-9_\s.\#>:\-()]+$/D',$block['selector'])) self::fail();
            if (!is_int($block['index']) || $block['index']<0 || $block['index']>2000) self::fail();
            if (!in_array($block['position'],['before','after'],true)) self::fail();
            if (!in_array($block['width'],['normal','wide','full'],true)) self::fail();
        }
        return $blocks;
    }
    private static function fail(): void {throw ValidationException::withMessages(['document'=>'Invalid inserted image blocks.']);}
}
