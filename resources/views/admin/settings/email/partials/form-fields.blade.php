@php
    $emailSetting = $emailSetting ?? null;
    $isEdit = $isEdit ?? false;
    $providerValue = old('provider', $emailSetting?->provider);
@endphp

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-server"></i> اختر المزود</h6>
    <div class="users-form-grid">
        <div class="users-form-group users-form-group--full">
            <label for="provider" class="users-form-label">
                <i class="fas fa-envelope-open-text"></i>
                مزود البريد
                <span class="users-form-required">*</span>
            </label>
            <select name="provider" id="provider" class="users-form-select @error('provider') is-invalid @enderror" required>
                <option value="">— اختر المزود —</option>
                @foreach ($providers as $key => $provider)
                    <option value="{{ $key }}" {{ $providerValue == $key ? 'selected' : '' }}>
                        {{ $provider['name'] }}
                    </option>
                @endforeach
            </select>
            <span class="users-form-hint">اختر Gmail أو Outlook للحصول على إعدادات جاهزة، أو «إعدادات مخصصة» للتكوين اليدوي</span>
            @error('provider')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-cog"></i> إعدادات SMTP</h6>
    <div class="users-form-grid">
        <div class="users-form-group users-form-group--full">
            <label for="mail_host" class="users-form-label">
                <i class="fas fa-network-wired"></i>
                SMTP Host
                <span class="users-form-required">*</span>
            </label>
            <input type="text" name="mail_host" id="mail_host"
                class="users-form-input @error('mail_host') is-invalid @enderror"
                value="{{ old('mail_host', $emailSetting?->mail_host) }}"
                placeholder="smtp.gmail.com" dir="ltr" required>
            @error('mail_host')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="mail_port" class="users-form-label">
                <i class="fas fa-plug"></i>
                Port
                <span class="users-form-required">*</span>
            </label>
            <input type="number" name="mail_port" id="mail_port"
                class="users-form-input @error('mail_port') is-invalid @enderror"
                value="{{ old('mail_port', $emailSetting?->mail_port ?? 587) }}"
                placeholder="587" dir="ltr" required>
            <span class="users-form-hint">587 (TLS)، 465 (SSL)، 25</span>
            @error('mail_port')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="mail_encryption" class="users-form-label">
                <i class="fas fa-shield-alt"></i>
                التشفير
                <span class="users-form-required">*</span>
            </label>
            <select name="mail_encryption" id="mail_encryption" class="users-form-select @error('mail_encryption') is-invalid @enderror" required>
                <option value="tls" {{ old('mail_encryption', $emailSetting?->mail_encryption ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS (موصى به)</option>
                <option value="ssl" {{ old('mail_encryption', $emailSetting?->mail_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                <option value="none" {{ old('mail_encryption', $emailSetting?->mail_encryption) == 'none' ? 'selected' : '' }}>بدون تشفير</option>
            </select>
            @error('mail_encryption')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group users-form-group--full">
            <label for="mail_username" class="users-form-label">
                <i class="fas fa-user"></i>
                اسم المستخدم / البريد
                <span class="users-form-required">*</span>
            </label>
            <input type="text" name="mail_username" id="mail_username"
                class="users-form-input @error('mail_username') is-invalid @enderror"
                value="{{ old('mail_username', $emailSetting?->mail_username) }}"
                placeholder="your-email@gmail.com" dir="ltr" required>
            @error('mail_username')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group users-form-group--full">
            <label for="mail_password" class="users-form-label">
                <i class="fas fa-key"></i>
                كلمة المرور
                @if (!$isEdit)
                    <span class="users-form-required">*</span>
                @endif
            </label>
            <div class="email-password-wrap">
                <input type="password" name="mail_password" id="mail_password"
                    class="users-form-input @error('mail_password') is-invalid @enderror"
                    placeholder="{{ $isEdit ? 'اتركه فارغاً للاحتفاظ بكلمة المرور الحالية' : '••••••••' }}"
                    {{ $isEdit ? '' : 'required' }}>
                <button type="button" class="email-password-toggle" id="email-password-toggle" aria-label="إظهار كلمة المرور">
                    <i class="fas fa-eye" id="email-password-toggle-icon"></i>
                </button>
            </div>
            @if ($isEdit)
                <span class="users-form-hint">اترك الحقل فارغاً للاحتفاظ بكلمة المرور الحالية</span>
            @endif
            @error('mail_password')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
            <div class="email-form-alert">
                <i class="fas fa-info-circle"></i>
                <span>
                    <strong>ملاحظة لـ Gmail:</strong> استخدم «App Password» وليس كلمة مرور حسابك العادية.
                    <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">إنشاء App Password</a>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-paper-plane"></i> إعدادات البريد المرسل</h6>
    <div class="users-form-grid">
        <div class="users-form-group">
            <label for="mail_from_address" class="users-form-label">
                <i class="fas fa-at"></i>
                البريد المرسل
                <span class="users-form-required">*</span>
            </label>
            <input type="email" name="mail_from_address" id="mail_from_address"
                class="users-form-input @error('mail_from_address') is-invalid @enderror"
                value="{{ old('mail_from_address', $emailSetting?->mail_from_address) }}"
                placeholder="noreply@example.com" dir="ltr" required>
            <span class="users-form-hint">البريد الذي سيظهر كمرسل</span>
            @error('mail_from_address')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="mail_from_name" class="users-form-label">
                <i class="fas fa-signature"></i>
                اسم المرسل
                <span class="users-form-required">*</span>
            </label>
            <input type="text" name="mail_from_name" id="mail_from_name"
                class="users-form-input @error('mail_from_name') is-invalid @enderror"
                value="{{ old('mail_from_name', $emailSetting?->mail_from_name ?? config('app.name')) }}"
                placeholder="نظام إدارة التعلم" required>
            <span class="users-form-hint">الاسم الذي سيظهر كمرسل</span>
            @error('mail_from_name')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
