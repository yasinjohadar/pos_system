@php
    use App\Support\PhoneNumber;

    $name = $name ?? 'phone';
    $inputId = $id ?? $name;
    $labelText = $label ?? 'رقم الهاتف';
    $required = $required ?? false;
    $value = $value ?? null;
    $parsed = PhoneNumber::parse(old($name, $value));
    $countryCode = old("{$name}_country_code", $parsed['country_code']);
    $local = old("{$name}_local", $parsed['local']);
    $countries = config('phone_countries', []);
    $hasError = $errors->has($name);

    $selectedCountry = collect($countries)->firstWhere('code', (string) $countryCode)
        ?? $countries[0]
        ?? ['iso' => 'SA', 'code' => '966', 'name_ar' => 'السعودية'];

    $flagUrl = fn (string $iso) => 'https://flagcdn.com/w40/' . strtolower($iso) . '.png';
@endphp

<div class="users-form-group users-phone-field">
    <label for="{{ $inputId }}_local" class="users-form-label">
        <i class="fas fa-phone"></i>
        {{ $labelText }}
        @if ($required)
            <span class="users-form-required">*</span>
        @endif
    </label>

    <div class="users-phone-input @if($hasError) is-invalid @endif" data-phone-field="{{ $name }}">
        <div class="users-phone-country">
            <input type="hidden"
                id="{{ $inputId }}_country_code"
                name="{{ $name }}_country_code"
                class="users-phone-input__country"
                value="{{ $countryCode }}">

            <button type="button"
                class="users-phone-country__toggle"
                aria-haspopup="listbox"
                aria-expanded="false"
                aria-label="اختر رمز الدولة">
                <img src="{{ $flagUrl($selectedCountry['iso']) }}"
                    alt=""
                    class="users-phone-country__flag"
                    width="22"
                    height="16"
                    loading="lazy">
                <span class="users-phone-country__dial">+{{ $selectedCountry['code'] }}</span>
                <i class="fas fa-chevron-down users-phone-country__chevron" aria-hidden="true"></i>
            </button>

            <ul class="users-phone-country__menu" role="listbox" hidden>
                @foreach ($countries as $country)
                    <li role="option"
                        class="users-phone-country__option {{ (string) $countryCode === (string) $country['code'] ? 'is-selected' : '' }}"
                        data-code="{{ $country['code'] }}"
                        data-iso="{{ $country['iso'] }}"
                        data-name="{{ $country['name_ar'] }}"
                        data-flag="{{ $flagUrl($country['iso']) }}"
                        aria-selected="{{ (string) $countryCode === (string) $country['code'] ? 'true' : 'false' }}">
                        <img src="{{ $flagUrl($country['iso']) }}"
                            alt=""
                            class="users-phone-country__flag"
                            width="22"
                            height="16"
                            loading="lazy">
                        <span class="users-phone-country__option-dial">+{{ $country['code'] }}</span>
                        <span class="users-phone-country__option-name">{{ $country['name_ar'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <input
            type="tel"
            id="{{ $inputId }}_local"
            name="{{ $name }}_local"
            class="users-form-input users-phone-input__local @if($hasError) is-invalid @enderror"
            value="{{ $local }}"
            placeholder="501234567"
            dir="ltr"
            inputmode="numeric"
            autocomplete="off"
            @if ($required) required @endif
        >

        <input type="hidden" name="{{ $name }}" id="{{ $inputId }}" value="{{ old($name, $value) }}">
    </div>

    <span class="users-form-hint">أدخل الرقم بدون صفر في البداية</span>

    @error($name)
        <div class="users-form-error">{{ $message }}</div>
    @enderror
</div>
