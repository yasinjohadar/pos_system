@extends('admin.layouts.master')

@section('page-title')
    إعدادات البريد الإلكتروني
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">إعدادات البريد الإلكتروني (SMTP)</h5>
                    <a href="{{ route('admin.settings.email.create') }}" class="users-btn-create">
                        <i class="fas fa-plus"></i>
                        إضافة إعدادات جديدة
                    </a>
                </div>

                @if ($activeSettings)
                    <div class="email-active-card">
                        <div class="email-active-card__header">
                            <i class="fas fa-check-circle"></i>
                            الإعدادات النشطة حالياً
                        </div>
                        <div class="email-active-card__grid">
                            <div class="email-active-card__item">
                                <span class="email-active-card__label">المزود</span>
                                <strong>{{ $providers[$activeSettings->provider]['name'] ?? 'مخصص' }}</strong>
                            </div>
                            <div class="email-active-card__item">
                                <span class="email-active-card__label">البريد المرسل</span>
                                <strong dir="ltr">{{ $activeSettings->mail_from_address }}</strong>
                            </div>
                            <div class="email-active-card__item">
                                <span class="email-active-card__label">التشفير</span>
                                <strong>{{ strtoupper($activeSettings->mail_encryption) }}</strong>
                            </div>
                            <div class="email-active-card__item">
                                <span class="email-active-card__label">آخر اختبار</span>
                                <strong>
                                    @if ($activeSettings->last_tested_at)
                                        {{ $activeSettings->last_tested_at->diffForHumans() }}
                                    @else
                                        لم يُختبر
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="email-form-alert email-form-alert--warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>
                            <strong>تنبيه:</strong> لا توجد إعدادات بريد نشطة. أضف وفعّل إعدادات لإرسال البريد من النظام.
                        </span>
                    </div>
                @endif

                <div class="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table users-table--email">
                            <thead>
                                <tr>
                                    <th>المزود</th>
                                    <th>SMTP Host</th>
                                    <th>Port</th>
                                    <th>البريد</th>
                                    <th>التشفير</th>
                                    <th>الحالة</th>
                                    <th>آخر اختبار</th>
                                    <th class="text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.settings.email.partials.table-rows', [
                                    'settings' => $settings,
                                    'providers' => $providers,
                                ])
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.settings.email.partials.test-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        function openEmailTestModal(settingId, defaultEmail) {
            document.getElementById('email-test-setting-id').value = settingId;
            document.getElementById('email-test-input').value = defaultEmail || '';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('email-test-modal')).show();
        }

        document.getElementById('email-test-send-btn').addEventListener('click', async function () {
            var settingId = document.getElementById('email-test-setting-id').value;
            var testEmail = document.getElementById('email-test-input').value.trim();
            var btn = this;

            if (!testEmail) {
                AdminPremium.showToast('الرجاء إدخال بريد إلكتروني صحيح', 'error');
                return;
            }

            var originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';

            try {
                var response = await fetch('/admin/settings/email/' + settingId + '/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ test_email: testEmail }),
                });

                var result = await response.json();

                if (result.success) {
                    AdminPremium.showToast(result.message, 'success');
                    bootstrap.Modal.getInstance(document.getElementById('email-test-modal')).hide();
                    window.location.reload();
                } else {
                    AdminPremium.showToast(result.message, 'error');
                }
            } catch (error) {
                AdminPremium.showToast('حدث خطأ أثناء إرسال البريد الاختباري', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    </script>
@stop
