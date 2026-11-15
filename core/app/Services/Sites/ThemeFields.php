<?php
namespace App\Services\Sites;

/** Known, site-specific editable fields, compiled from the shipped Blade themes. */
final class ThemeFields {
    public static function catalog(): array {
        $data = json_decode(file_get_contents(base_path('resources/site-editor/theme-fields.json')), true);
        return is_array($data) ? $data : [];
    }
    public static function forTemplate(string $template): array {
        return self::catalog()[$template] ?? ['copy'=>[], 'images'=>[]];
    }
    public static function text(array $doc, string $key, string $default): string {
        return isset($doc['theme_copy'][$key]) && is_string($doc['theme_copy'][$key]) ? $doc['theme_copy'][$key] : $default;
    }
    public static function image(array $doc, string $key, string $default): string {
        return isset($doc['theme_images'][$key]) && is_string($doc['theme_images'][$key]) ? $doc['theme_images'][$key] : $default;
    }
    public static function validate(array &$doc, string $template): void {
        $definition = self::forTemplate($template);
        foreach (['theme_copy'=>'copy','theme_images'=>'images'] as $field=>$category) {
            $values = $doc[$field] ?? [];
            if (!is_array($values) || count($values)>100 || array_is_list($values) && $values!==[]) self::reject($field);
            foreach ($values as $key=>$value) {
                if (!is_string($key) || !array_key_exists($key, $definition[$category]) || !is_string($value)) self::reject($field);
                if (SiteDocument::characterLength($value)>($category==='copy'?500:500)) self::reject($field);
                if ($category==='images' && !self::safeImage($value)) self::reject($field);
            }
            $doc[$field]=$values;
        }
    }
    public static function safeImage(string $url): bool {
        if (preg_match('~^/(?!/)[a-zA-Z0-9/_\-%.]+$~D', $url)) return true;
        return (bool) preg_match('~^https://[a-z0-9.-]+(?:/[a-zA-Z0-9/_\-?&=%.]*)?$~iD', $url);
    }
    private static function reject(string $field): void {
        throw \Illuminate\Validation\ValidationException::withMessages(['document'=>'Invalid '.$field]);
    }
}
