@php
    $settings = $settings ?? [];
    $currentProvider = old('whatsapp_provider', $settings['whatsapp_provider'] ?? 'meta');
    $customApiHeaders = old('custom_api_headers', $settings['custom_api_headers'] ?? []);
    if (is_array($customApiHeaders)) {
        $customApiHeaders = json_encode($customApiHeaders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    $webhookPath = old('webhook_path', $settings['webhook_path'] ?? '/api/webhooks/whatsapp');
@endphp

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-cog"></i> الإعدادات العامة</h6>
    <div class="users-form-grid">
        <div class="users-form-group">
            <div class="users-form-switches">
                <div class="users-form-switch">
                    <div class="users-form-switch__icon users-form-switch__icon--active">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="users-form-switch__info">
                        <span class="users-form-switch__title">تفعيل WhatsApp</span>
                        <span class="users-form-switch__desc">تشغيل أو إيقاف خدمة WhatsApp في النظام</span>
                    </div>
                    <label class="users-toggle users-toggle--compact">
                        <input type="checkbox" class="users-toggle-input users-form-toggle-input"
                            id="whatsapp_enabled" name="whatsapp_enabled" value="1"
                            data-label-on="مفعّل" data-label-off="معطّل"
                            {{ filter_var(old('whatsapp_enabled', $settings['whatsapp_enabled'] ?? false), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                        <span class="users-toggle-track"><span class="users-toggle-thumb"></span></span>
                        <span class="users-toggle-label">{{ filter_var(old('whatsapp_enabled', $settings['whatsapp_enabled'] ?? false), FILTER_VALIDATE_BOOLEAN) ? 'مفعّل' : 'معطّل' }}</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="users-form-group">
            <label for="whatsapp_provider" class="users-form-label">
                <i class="fas fa-server"></i>
                المزود
                <span class="users-form-required">*</span>
            </label>
            <select class="users-form-select @error('whatsapp_provider') is-invalid @enderror"
                name="whatsapp_provider" id="whatsapp_provider" required>
                <option value="meta" {{ $currentProvider === 'meta' ? 'selected' : '' }}>Meta (WhatsApp Cloud API)</option>
                <option value="custom_api" {{ $currentProvider === 'custom_api' ? 'selected' : '' }}>Custom API</option>
                <option value="whatsapp_web" {{ $currentProvider === 'whatsapp_web' ? 'selected' : '' }}>WhatsApp Web (QR Code)</option>
            </select>
            @error('whatsapp_provider')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group users-form-group--full" id="provider-web-links" @if($currentProvider !== 'whatsapp_web') hidden @endif>
            <div class="users-form-actions" style="justify-content: flex-start; padding-top: 0;">
                <a href="{{ route('admin.whatsapp-web-settings.index') }}" class="users-btn-secondary">
                    <i class="fas fa-cog"></i>
                    إعدادات WhatsApp Web
                </a>
                <a href="{{ route('admin.whatsapp-web.connect') }}" class="users-btn-secondary">
                    <i class="fas fa-qrcode"></i>
                    ربط WhatsApp Web
                </a>
            </div>
        </div>
    </div>
</div>

<div class="users-form-card__section" id="meta-settings" @if($currentProvider !== 'meta') hidden @endif>
    <h6 class="users-form-section-title"><i class="fab fa-facebook"></i> إعدادات Meta (WhatsApp Cloud API)</h6>
    <div class="users-form-grid">
        <div class="users-form-group">
            <label for="api_version" class="users-form-label">إصدار API <span class="users-form-required">*</span></label>
            <input type="text" class="users-form-input @error('api_version') is-invalid @enderror"
                name="api_version" id="api_version"
                value="{{ old('api_version', $settings['api_version'] ?? 'v20.0') }}"
                placeholder="v20.0" dir="ltr">
            @error('api_version')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="phone_number_id" class="users-form-label">Phone Number ID <span class="users-form-required">*</span></label>
            <input type="text" class="users-form-input @error('phone_number_id') is-invalid @enderror"
                name="phone_number_id" id="phone_number_id"
                value="{{ old('phone_number_id', $settings['phone_number_id'] ?? '') }}"
                placeholder="رقم معرف رقم الهاتف" dir="ltr">
            @error('phone_number_id')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="waba_id" class="users-form-label">WABA ID</label>
            <input type="text" class="users-form-input @error('waba_id') is-invalid @enderror"
                name="waba_id" id="waba_id"
                value="{{ old('waba_id', $settings['waba_id'] ?? '') }}"
                placeholder="معرف WhatsApp Business Account" dir="ltr">
            @error('waba_id')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="access_token" class="users-form-label">Access Token</label>
            <input type="password" class="users-form-input @error('access_token') is-invalid @enderror"
                name="access_token" id="access_token" value=""
                placeholder="اتركه فارغاً للحفاظ على القيمة الحالية" dir="ltr">
            <span class="users-form-hint">اتركه فارغاً إذا لم ترغب بتغييره</span>
            @error('access_token')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="verify_token" class="users-form-label">Verify Token <span class="users-form-required">*</span></label>
            <input type="text" class="users-form-input @error('verify_token') is-invalid @enderror"
                name="verify_token" id="verify_token"
                value="{{ old('verify_token', $settings['verify_token'] ?? '') }}"
                placeholder="رمز التحقق للـ Webhook" dir="ltr">
            @error('verify_token')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="app_secret" class="users-form-label">App Secret</label>
            <input type="password" class="users-form-input @error('app_secret') is-invalid @enderror"
                name="app_secret" id="app_secret" value=""
                placeholder="اتركه فارغاً للحفاظ على القيمة الحالية" dir="ltr">
            <span class="users-form-hint">للتوقيع الرقمي للـ Webhook</span>
            @error('app_secret')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="users-form-card__section" id="custom-api-settings" @if($currentProvider !== 'custom_api') hidden @endif>
    <h6 class="users-form-section-title"><i class="fas fa-code"></i> إعدادات Custom API</h6>
    <div class="users-form-grid">
        <div class="users-form-group users-form-group--full">
            <label for="custom_api_url" class="users-form-label">API URL <span class="users-form-required">*</span></label>
            <input type="url" class="users-form-input @error('custom_api_url') is-invalid @enderror"
                name="custom_api_url" id="custom_api_url"
                value="{{ old('custom_api_url', $settings['custom_api_url'] ?? '') }}"
                placeholder="https://api.example.com/send" dir="ltr">
            @error('custom_api_url')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="custom_api_method" class="users-form-label">HTTP Method</label>
            <select class="users-form-select" name="custom_api_method" id="custom_api_method">
                <option value="POST" {{ old('custom_api_method', $settings['custom_api_method'] ?? 'POST') === 'POST' ? 'selected' : '' }}>POST</option>
                <option value="GET" {{ old('custom_api_method', $settings['custom_api_method'] ?? '') === 'GET' ? 'selected' : '' }}>GET</option>
            </select>
        </div>

        <div class="users-form-group">
            <label for="custom_api_key" class="users-form-label">API Key</label>
            <input type="password" class="users-form-input" name="custom_api_key" id="custom_api_key"
                value="" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية" dir="ltr">
        </div>

        <div class="users-form-group users-form-group--full">
            <label for="custom_api_headers" class="users-form-label">Custom Headers (JSON)</label>
            <textarea class="users-form-input" name="custom_api_headers" id="custom_api_headers" rows="4"
                placeholder='{"Authorization": "Bearer token"}' dir="ltr">{{ $customApiHeaders }}</textarea>
            <span class="users-form-hint">أدخل headers كـ JSON object</span>
        </div>
    </div>
</div>

<div class="users-form-card__section" id="whatsapp-web-settings" @if($currentProvider !== 'whatsapp_web') hidden @endif>
    <h6 class="users-form-section-title"><i class="fas fa-qrcode"></i> WhatsApp Web</h6>
    <div class="email-form-alert">
        <i class="fas fa-info-circle"></i>
        <span>لإعداد WhatsApp Web، استخدم صفحة الإعدادات المخصصة أو امسح رمز QR للربط.</span>
    </div>
    <div class="users-form-actions" style="justify-content: flex-start; padding-top: 0.75rem;">
        <a href="{{ route('admin.whatsapp-web-settings.index') }}" class="users-btn-secondary">
            <i class="fas fa-cog"></i>
            فتح إعدادات WhatsApp Web
        </a>
        <a href="{{ route('admin.whatsapp-web.connect') }}" class="users-btn-secondary">
            <i class="fas fa-qrcode"></i>
            ربط WhatsApp Web
        </a>
    </div>
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-link"></i> إعدادات Webhook</h6>
    <div class="users-form-grid">
        <div class="users-form-group">
            <label for="webhook_path" class="users-form-label">Webhook Path</label>
            <input type="text" class="users-form-input @error('webhook_path') is-invalid @enderror"
                name="webhook_path" id="webhook_path" value="{{ $webhookPath }}"
                placeholder="/api/webhooks/whatsapp" dir="ltr">
            @error('webhook_path')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="default_from" class="users-form-label">Default From</label>
            <input type="text" class="users-form-input" name="default_from" id="default_from"
                value="{{ old('default_from', $settings['default_from'] ?? '') }}"
                placeholder="رقم الهاتف الافتراضي" dir="ltr">
        </div>

        <div class="users-form-group users-form-group--full">
            <div class="users-form-switches">
                <div class="users-form-switch">
                    <div class="users-form-switch__icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="users-form-switch__info">
                        <span class="users-form-switch__title">التحقق الصارم من التوقيع</span>
                        <span class="users-form-switch__desc">يُنصح بتركه مفعّلاً للأمان</span>
                    </div>
                    <label class="users-toggle users-toggle--compact">
                        <input type="checkbox" class="users-toggle-input users-form-toggle-input"
                            id="strict_signature" name="strict_signature" value="1"
                            data-label-on="مفعّل" data-label-off="معطّل"
                            {{ filter_var(old('strict_signature', $settings['strict_signature'] ?? true), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                        <span class="users-toggle-track"><span class="users-toggle-thumb"></span></span>
                        <span class="users-toggle-label">{{ filter_var(old('strict_signature', $settings['strict_signature'] ?? true), FILTER_VALIDATE_BOOLEAN) ? 'مفعّل' : 'معطّل' }}</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="users-form-group users-form-group--full">
            <div class="email-form-alert">
                <i class="fas fa-info-circle"></i>
                <span>
                    <strong>Webhook URL:</strong>
                    <code class="users-color-value">{{ url($webhookPath) }}</code>
                    — استخدم هذا الرابط في Meta Developer Console
                </span>
            </div>
        </div>
    </div>
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-reply"></i> الرد التلقائي</h6>
    <div class="users-form-grid">
        <div class="users-form-group users-form-group--full">
            <div class="users-form-switches">
                <div class="users-form-switch">
                    <div class="users-form-switch__icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="users-form-switch__info">
                        <span class="users-form-switch__title">تفعيل الرد التلقائي</span>
                        <span class="users-form-switch__desc">إرسال رد تلقائي عند استلام رسائل واردة</span>
                    </div>
                    <label class="users-toggle users-toggle--compact">
                        <input type="checkbox" class="users-toggle-input users-form-toggle-input"
                            id="auto_reply" name="auto_reply" value="1"
                            data-label-on="مفعّل" data-label-off="معطّل"
                            {{ filter_var(old('auto_reply', $settings['auto_reply'] ?? false), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                        <span class="users-toggle-track"><span class="users-toggle-thumb"></span></span>
                        <span class="users-toggle-label">{{ filter_var(old('auto_reply', $settings['auto_reply'] ?? false), FILTER_VALIDATE_BOOLEAN) ? 'مفعّل' : 'معطّل' }}</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="users-form-group users-form-group--full">
            <label for="auto_reply_message" class="users-form-label">رسالة الرد التلقائي</label>
            <textarea class="users-form-input @error('auto_reply_message') is-invalid @enderror"
                name="auto_reply_message" id="auto_reply_message" rows="3"
                placeholder="شكراً لك، تم استلام رسالتك. سنرد عليك قريباً.">{{ old('auto_reply_message', $settings['auto_reply_message'] ?? 'شكراً لك، تم استلام رسالتك. سنرد عليك قريباً.') }}</textarea>
            @error('auto_reply_message')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-sliders-h"></i> إعدادات متقدمة</h6>
    <div class="users-form-grid">
        <div class="users-form-group">
            <label for="timeout" class="users-form-label">Timeout (ثوانٍ)</label>
            <input type="number" class="users-form-input @error('timeout') is-invalid @enderror"
                name="timeout" id="timeout"
                value="{{ old('timeout', $settings['timeout'] ?? 30) }}"
                min="1" max="300" placeholder="30">
            <span class="users-form-hint">المهلة الزمنية لانتظار استجابة API</span>
            @error('timeout')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div id="test-connection-result" class="storage-test-result" hidden></div>
