<?php
declare(strict_types=1);
namespace App\Booking\Public\Checkout;

/** Validated customer contact data; never trust client-provided booking prices or identities. */
final class CheckoutCustomer {
    public static function parse(array $value): array {
        $allowed = ['first_name','last_name','email','phone','address','city','state','zip','notes','consent'];
        foreach ($value as $key => $_) {
            if (!in_array($key, $allowed, true)) throw new \InvalidArgumentException('Unexpected customer field');
        }
        $result = [];
        foreach (['first_name'=>80,'last_name'=>80,'address'=>250] as $field=>$limit) {
            $result[$field] = self::field($value[$field]??null,$limit,true);
        }
        // Cleanto's client_email is bounded to 200 characters; reject at the form boundary.
        $result['email']=self::field($value['email']??null,200,true);
        if (!filter_var($result['email'], FILTER_VALIDATE_EMAIL)) throw new \InvalidArgumentException('Invalid email');
        $result['email']=strtolower($result['email']);
        // Do not silently normalize control characters into a valid telephone number.
        if (is_string($value['phone'] ?? null) && preg_match('/[\x00-\x1F\x7F]/', $value['phone'])) {
            throw new \InvalidArgumentException('Invalid phone');
        }
        $phone = self::field($value['phone']??null,32,true);
        // Accept human-readable formatting, but persist the canonical number the
        // original Cleanto schema can actually store (15 characters maximum).
        // Reject rather than truncate country codes or subscriber digits.
        if (!preg_match('/^\+?[0-9 ()-]{7,32}$/D', $phone)) {
            throw new \InvalidArgumentException('Invalid phone');
        }
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) < 7 || strlen($digits) > 15) {
            throw new \InvalidArgumentException('Invalid phone length');
        }
        $result['phone'] = (strpos($phone, '+') === 0 ? '+' : '') . $digits;
        if (strlen($result['phone']) > 15) {
            throw new \InvalidArgumentException('Phone exceeds Cleanto storage limit');
        }
        // Structured address fields are optional for legacy callers, but are preserved
        // when the modern public checkout supplies them.
        foreach (['city'=>100,'state'=>100,'zip'=>24] as $field=>$limit) {
            $result[$field]=self::field($value[$field]??'', $limit, false);
        }
        $result['notes']=self::field($value['notes']??'',1000,false);
        if(($value['consent']??null)!==true) throw new \InvalidArgumentException('Consent required');
        $result['consent']=true;
        return $result;
    }
    private static function field($v,int $limit,bool $required):string {
        if(!is_string($v)||strlen($v)>$limit*4||!preg_match('//u',$v))throw new \InvalidArgumentException('Invalid contact field');
        // Reject control characters instead of silently rewriting address or contact data.
        if (preg_match('/[\x00-\x1F\x7F]/u', $v)) throw new \InvalidArgumentException('Invalid contact controls');
        $v=trim($v);
        if($required && $v==='')throw new \InvalidArgumentException('Missing customer field');
        if(function_exists('mb_strlen') ? mb_strlen($v,'UTF-8')>$limit : preg_match_all('/./us',$v)>$limit)throw new \InvalidArgumentException('Customer field too long');
        return $v;
    }
}
