<?php
declare(strict_types=1);
namespace Titan\BookingSidecar;

/** Cleanto's original client_personal_info format. No PHP objects or client authority fields. */
final class BookingPersonalInfo {
    private const FIELDS = ['zip'=>24, 'address'=>250, 'city'=>100, 'state'=>100, 'notes'=>1000];
    public static function parse(array $input): array {
        foreach ($input as $key => $_) {
            if (!array_key_exists($key, self::FIELDS)) {
                throw new \InvalidArgumentException('Unsupported customer location field');
            }
        }
        $result = [];
        foreach (self::FIELDS as $key => $limit) {
            $value = $input[$key] ?? '';
            if (!is_string($value) || strlen($value) > $limit * 4 || !preg_match('//u', $value)) {
                throw new \InvalidArgumentException('Invalid customer location');
            }
            if (preg_match('/[\x00-\x1F\x7F]/', $value)) {
                throw new \InvalidArgumentException('Invalid customer location controls');
            }
            $value = trim($value);
            if (preg_match('/[\x00-\x1F\x7F]/', $value) || preg_match_all('/./us', $value) > $limit) {
                throw new \InvalidArgumentException('Invalid customer location length or controls');
            }
            $result[$key] = $value;
        }
        return $result;
    }
    public static function encode(array $input): string {
        $data = self::parse($input);
        // Cleanto's own front/booking_complete.php uses this eight-key structure.
        // Status fields are initialized to empty, never accepted from a client.
        return base64_encode(serialize([
            'zip'=>$data['zip'], 'address'=>$data['address'],
            'city'=>$data['city'], 'state'=>$data['state'],
            'notes'=>$data['notes'], 'vc_status'=>'',
            'p_status'=>'', 'contact_status'=>'',
        ]));
    }
}
