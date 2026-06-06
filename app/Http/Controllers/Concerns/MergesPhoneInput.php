<?php

namespace App\Http\Controllers\Concerns;

use App\Support\PhoneNumber;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

trait MergesPhoneInput
{
    protected function mergePhoneInput(Request $request, string $field = 'phone'): void
    {
        $request->merge([
            $field => PhoneNumber::toE164(
                (string) $request->input("{$field}_country_code", PhoneNumber::DEFAULT_COUNTRY_CODE),
                $request->input("{$field}_local")
            ),
        ]);
    }

    /**
     * @param  mixed  $uniqueRule  Rule instance or null
     * @return array<string, array<int, mixed>>
     */
    protected function phoneRules(string $field = 'phone', mixed $uniqueRule = null): array
    {
        $rules = ['nullable', 'string', 'regex:/^\+[1-9]\d{1,14}$/'];

        if ($uniqueRule !== null) {
            $rules[] = $uniqueRule;
        }

        return [$field => $rules];
    }

    protected function validatePhoneLocalIfPresent(Request $request, Validator $validator, string $field = 'phone'): void
    {
        $validator->after(function (Validator $v) use ($request, $field) {
            $local = trim((string) $request->input("{$field}_local", ''));

            if ($local === '') {
                return;
            }

            if (! PhoneNumber::isValidE164($request->input($field))) {
                $v->errors()->add(
                    $field,
                    'رقم الهاتف غير صحيح — أدخل الرقم بدون صفر في البداية'
                );
            }
        });
    }

    /**
     * @param  array<string, mixed>  $rules
     * @param  array<string, string>  $messages
     * @return array<string, mixed>
     */
    protected function validateRequestWithPhone(
        Request $request,
        array $rules,
        array $messages = [],
        string $field = 'phone'
    ): array {
        $this->mergePhoneInput($request, $field);

        $validator = ValidatorFacade::make($request->all(), $rules, $messages);
        $this->validatePhoneLocalIfPresent($request, $validator, $field);

        return $validator->validate();
    }
}
