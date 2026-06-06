<?php

namespace App\Support;

class PhoneNumber
{
    public const DEFAULT_COUNTRY_CODE = '966';

    public const E164_REGEX = '/^\+[1-9]\d{1,14}$/';

    /**
     * @return array{country_code: string, local: string}
     */
    public static function parse(?string $phone): array
    {
        $default = [
            'country_code' => self::DEFAULT_COUNTRY_CODE,
            'local' => '',
        ];

        if ($phone === null || trim($phone) === '') {
            return $default;
        }

        $phone = trim($phone);
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '') {
            return $default;
        }

        if (str_starts_with($phone, '+')) {
            return self::parseInternationalDigits($digits);
        }

        if (preg_match('/^05\d{8}$/', $digits)) {
            return [
                'country_code' => self::DEFAULT_COUNTRY_CODE,
                'local' => substr($digits, 1),
            ];
        }

        if (preg_match('/^5\d{8}$/', $digits)) {
            return [
                'country_code' => self::DEFAULT_COUNTRY_CODE,
                'local' => $digits,
            ];
        }

        $matched = self::matchCountryCodeFromDigits($digits);
        if ($matched !== null) {
            return $matched;
        }

        return [
            'country_code' => self::DEFAULT_COUNTRY_CODE,
            'local' => ltrim($digits, '0'),
        ];
    }

    public static function toE164(string $countryCode, ?string $local): ?string
    {
        $countryCode = preg_replace('/\D+/', '', $countryCode) ?? '';
        $local = preg_replace('/\D+/', '', (string) $local) ?? '';

        if ($countryCode === '' || $local === '') {
            return null;
        }

        $local = ltrim($local, '0');
        if ($local === '') {
            return null;
        }

        $e164 = '+' . $countryCode . $local;

        return self::isValidE164($e164) ? $e164 : null;
    }

    public static function isValidE164(?string $phone): bool
    {
        if ($phone === null || $phone === '') {
            return false;
        }

        return (bool) preg_match(self::E164_REGEX, $phone);
    }

    /**
     * @return array<int, string>
     */
    public static function countryCodesLongestFirst(): array
    {
        static $codes = null;

        if ($codes !== null) {
            return $codes;
        }

        $codes = array_map(
            fn (array $country) => $country['code'],
            config('phone_countries', [])
        );

        usort($codes, fn (string $a, string $b) => strlen($b) <=> strlen($a));

        return $codes;
    }

    /**
     * @return array{country_code: string, local: string}
     */
    private static function parseInternationalDigits(string $digits): array
    {
        $matched = self::matchCountryCodeFromDigits($digits);
        if ($matched !== null) {
            return $matched;
        }

        return [
            'country_code' => self::DEFAULT_COUNTRY_CODE,
            'local' => ltrim($digits, '0'),
        ];
    }

    /**
     * @return array{country_code: string, local: string}|null
     */
    private static function matchCountryCodeFromDigits(string $digits): ?array
    {
        foreach (self::countryCodesLongestFirst() as $code) {
            if (str_starts_with($digits, $code)) {
                $local = substr($digits, strlen($code));
                $local = ltrim($local, '0');

                if ($local !== '') {
                    return [
                        'country_code' => $code,
                        'local' => $local,
                    ];
                }
            }
        }

        return null;
    }
}
