# Phone Input Component — Design Spec

**Date:** 2026-06-05  
**Status:** Implemented

## Summary

Reusable Premium phone input with country code dropdown (Arab countries, Turkey, Germany), flag emoji, and E.164 storage in the existing `phone` column.

## Decisions

| Topic | Choice |
|-------|--------|
| Storage | Single `phone` field in E.164 format (`+966501234567`) |
| Default country | Saudi Arabia (+966) on create; auto-parse on edit |
| Scope | Users, branches, customers, suppliers |
| Local input | Without leading zero (e.g. `501234567`) |

## Architecture

- **Config:** `config/phone_countries.php` — curated country list
- **Helper:** `App\Support\PhoneNumber` — parse, toE164, isValidE164
- **UI:** `resources/views/admin/components/premium/phone-input.blade.php`
- **JS:** `AdminPremium.initPhoneInputs()` in `public/assets/js/admin-premium.js`
- **CSS:** `.users-phone-*` in `public/assets/css/admin-premium.css`
- **Backend:** `App\Http\Controllers\Concerns\MergesPhoneInput` trait

## Form fields

- `phone_country_code` — select (not persisted)
- `phone_local` — tel input (not persisted)
- `phone` — hidden E.164 (persisted)

Server merges `phone_country_code` + `phone_local` before validation.

## Validation

- `nullable`, `regex:/^\+[1-9]\d{1,14}$/`
- Custom after-validation when local is filled but E.164 is invalid
- Users: unique constraint on `phone`

## Legacy data

`PhoneNumber::parse()` supports:

- E.164 (`+966501234567`)
- Saudi local with zero (`0501234567`)
- Saudi local without zero (`501234567`)
- Digits with country prefix (longest match from config)

## Files touched

- `config/phone_countries.php`
- `app/Support/PhoneNumber.php`
- `app/Http/Controllers/Concerns/MergesPhoneInput.php`
- `resources/views/admin/components/premium/phone-input.blade.php`
- Controllers: User, Branch, Customer, Supplier
- Forms: users, branches, suppliers, customers create/edit
- `tests/Unit/PhoneNumberTest.php`

## Out of scope

- Warehouses (no phone field)
- Separate DB column for country code
- Full international country list
