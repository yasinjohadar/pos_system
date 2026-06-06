<?php

namespace Tests\Unit;

use App\Support\PhoneNumber;
use Tests\TestCase;

class PhoneNumberTest extends TestCase
{
    public function test_to_e164_builds_international_number(): void
    {
        $this->assertSame('+966501234567', PhoneNumber::toE164('966', '501234567'));
    }

    public function test_to_e164_returns_null_for_empty_local(): void
    {
        $this->assertNull(PhoneNumber::toE164('966', ''));
        $this->assertNull(PhoneNumber::toE164('966', null));
    }

    public function test_to_e164_strips_leading_zero_from_local(): void
    {
        $this->assertSame('+966501234567', PhoneNumber::toE164('966', '0501234567'));
    }

    public function test_parse_e164_number(): void
    {
        $this->assertSame(
            ['country_code' => '966', 'local' => '501234567'],
            PhoneNumber::parse('+966501234567')
        );
    }

    public function test_parse_legacy_saudi_with_leading_zero(): void
    {
        $this->assertSame(
            ['country_code' => '966', 'local' => '501234567'],
            PhoneNumber::parse('0501234567')
        );
    }

    public function test_parse_local_saudi_without_zero(): void
    {
        $this->assertSame(
            ['country_code' => '966', 'local' => '501234567'],
            PhoneNumber::parse('501234567')
        );
    }

    public function test_parse_german_number(): void
    {
        $this->assertSame(
            ['country_code' => '49', 'local' => '15123456789'],
            PhoneNumber::parse('+4915123456789')
        );
    }

    public function test_parse_empty_returns_default(): void
    {
        $this->assertSame(
            ['country_code' => '966', 'local' => ''],
            PhoneNumber::parse(null)
        );
    }

    public function test_is_valid_e164(): void
    {
        $this->assertTrue(PhoneNumber::isValidE164('+966501234567'));
        $this->assertFalse(PhoneNumber::isValidE164('0501234567'));
        $this->assertFalse(PhoneNumber::isValidE164(null));
    }
}
