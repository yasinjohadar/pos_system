@php
    $settings = $settings ?? [];
@endphp

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-server"></i> إعدادات Node.js Service</h6>
    <div class="users-form-grid">
        <div class="users-form-group users-form-group--full">
            <label for="whatsapp_web_service_url" class="users-form-label">
                رابط Node.js Service
                <span class="users-form-required">*</span>
            </label>
            <input type="url" class="users-form-input @error('whatsapp_web_service_url') is-invalid @enderror"
                name="whatsapp_web_service_url" id="whatsapp_web_service_url"
                value="{{ old('whatsapp_web_service_url', $settings['whatsapp_web_service_url'] ?? 'http://localhost:3000') }}"
                placeholder="http://localhost:3000" dir="ltr" required>
            <span class="users-form-hint">رابط خدمة Node.js التي تدير WhatsApp Web</span>
            @error('whatsapp_web_service_url')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group users-form-group--full">
            <label for="whatsapp_web_api_token" class="users-form-label">API Token</label>
            <input type="password" class="users-form-input @error('whatsapp_web_api_token') is-invalid @enderror"
                name="whatsapp_web_api_token" id="whatsapp_web_api_token"
                value="" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية" dir="ltr">
            <span class="users-form-hint">Token للتوثيق مع Node.js service (اختياري)</span>
            @error('whatsapp_web_api_token')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-clock"></i> إعدادات الفواصل الزمنية</h6>
    <div class="email-form-alert email-form-alert--warning mb-3">
        <i class="fas fa-exclamation-triangle"></i>
        <span><strong>مهم:</strong> الفواصل الزمنية تساعد في تجنب الحظر من WhatsApp. يُنصح بترك القيم الافتراضية.</span>
    </div>
    <div class="users-form-grid">
        <div class="users-form-group">
            <label for="delay_between_messages" class="users-form-label">الفاصل بين الرسائل (ث)</label>
            <input type="number" class="users-form-input" name="delay_between_messages" id="delay_between_messages"
                value="{{ old('delay_between_messages', $settings['delay_between_messages'] ?? 3) }}"
                min="1" max="60" placeholder="3">
        </div>

        <div class="users-form-group">
            <label for="delay_between_broadcasts" class="users-form-label">الفاصل بين الإرسال الجماعي (ث)</label>
            <input type="number" class="users-form-input" name="delay_between_broadcasts" id="delay_between_broadcasts"
                value="{{ old('delay_between_broadcasts', $settings['delay_between_broadcasts'] ?? 5) }}"
                min="1" max="60" placeholder="5">
        </div>

        <div class="users-form-group">
            <label for="max_messages_per_minute" class="users-form-label">الحد الأقصى / دقيقة</label>
            <input type="number" class="users-form-input" name="max_messages_per_minute" id="max_messages_per_minute"
                value="{{ old('max_messages_per_minute', $settings['max_messages_per_minute'] ?? 20) }}"
                min="1" max="100" placeholder="20">
        </div>

        <div class="users-form-group">
            <label for="min_delay" class="users-form-label">الحد الأدنى للفاصل العشوائي (ث)</label>
            <input type="number" class="users-form-input" name="min_delay" id="min_delay"
                value="{{ old('min_delay', $settings['min_delay'] ?? 2) }}"
                min="1" max="10" placeholder="2">
        </div>

        <div class="users-form-group">
            <label for="max_delay" class="users-form-label">الحد الأقصى للفاصل العشوائي (ث)</label>
            <input type="number" class="users-form-input" name="max_delay" id="max_delay"
                value="{{ old('max_delay', $settings['max_delay'] ?? 5) }}"
                min="1" max="10" placeholder="5">
        </div>

        <div class="users-form-group users-form-group--full">
            <div class="users-form-switches">
                <div class="users-form-switch">
                    <div class="users-form-switch__icon">
                        <i class="fas fa-random"></i>
                    </div>
                    <div class="users-form-switch__info">
                        <span class="users-form-switch__title">الفواصل العشوائية</span>
                        <span class="users-form-switch__desc">تجنب الأنماط الثابتة في الإرسال</span>
                    </div>
                    <label class="users-toggle users-toggle--compact">
                        <input type="checkbox" class="users-toggle-input users-form-toggle-input"
                            id="random_delay_enabled" name="random_delay_enabled" value="1"
                            data-label-on="مفعّل" data-label-off="معطّل"
                            {{ filter_var(old('random_delay_enabled', $settings['random_delay_enabled'] ?? true), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                        <span class="users-toggle-track"><span class="users-toggle-thumb"></span></span>
                        <span class="users-toggle-label">{{ filter_var(old('random_delay_enabled', $settings['random_delay_enabled'] ?? true), FILTER_VALIDATE_BOOLEAN) ? 'مفعّل' : 'معطّل' }}</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="test-connection-result" class="storage-test-result" hidden></div>
