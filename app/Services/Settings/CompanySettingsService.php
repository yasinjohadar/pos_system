<?php

namespace App\Services\Settings;

use App\Models\SystemSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanySettingsService
{
    public const GROUP = 'company';

    public const KEYS = [
        'company_name',
        'company_logo',
        'tax_number',
        'default_currency',
        'invoice_footer',
        'default_tax_id',
    ];

    public function getSettings(): array
    {
        $defaults = [
            'company_name' => config('app.name', 'POS System'),
            'company_logo' => null,
            'tax_number' => '',
            'default_currency' => 'SAR',
            'invoice_footer' => '',
            'default_tax_id' => null,
        ];

        foreach (self::KEYS as $key) {
            $value = SystemSetting::get($key, self::GROUP);
            if ($value !== null) {
                $defaults[$key] = $value;
            }
        }

        return $defaults;
    }

    public function updateSettings(array $data, ?UploadedFile $logo = null): void
    {
        if ($logo) {
            $oldLogo = SystemSetting::get('company_logo', self::GROUP);
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $data['company_logo'] = $logo->store('company', 'public');
        }

        foreach (self::KEYS as $key) {
            if (array_key_exists($key, $data)) {
                $type = $key === 'default_tax_id' ? 'integer' : 'string';
                SystemSetting::set($key, $data[$key] ?? '', $type, self::GROUP);
            }
        }
    }

    public function logoUrl(): ?string
    {
        $path = SystemSetting::get('company_logo', self::GROUP);
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
