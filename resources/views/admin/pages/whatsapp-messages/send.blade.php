@extends('admin.layouts.master')

@section('page-title')
    إرسال رسالة WhatsApp
@stop

@section('css')
    @include('admin.components.premium.styles')
    <style>@include('admin.pages.users.partials.form-styles')</style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>يرجى تصحيح الأخطاء التالية:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="users-header">
                    <h5 class="users-page-title">إرسال رسالة WhatsApp</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.whatsapp-messages.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-whatsapp',
                        'title' => 'رسالة جديدة',
                        'text' => 'أرسل رسالة نصية أو قالباً معتمداً عبر WhatsApp Business API.',
                        'tips' => [
                            'رقم الهاتف يبدأ بـ + ورمز الدولة',
                            'القوالب يجب أن تكون معتمدة في Meta',
                            'الإرسال الجماعي يشمل المستخدمين ذوي أرقام صحيحة',
                        ],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-paper-plane"></i> بيانات الإرسال</h6>
                        </div>
                        <form action="{{ route('admin.whatsapp-messages.broadcast') }}" method="POST"
                            id="message-form" class="users-form-card__body">
                            @csrf

                            <div class="users-form-card__section">
                                <h6 class="users-form-section-title"><i class="fas fa-share"></i> نوع الإرسال</h6>
                                <div class="storage-file-types">
                                    <label class="storage-file-type">
                                        <input type="radio" name="send_type" id="send_type_individual" value="individual"
                                            {{ old('send_type', 'individual') === 'individual' ? 'checked' : '' }} required>
                                        <span>إرسال فردي</span>
                                    </label>
                                    <label class="storage-file-type">
                                        <input type="radio" name="send_type" id="send_type_broadcast" value="broadcast"
                                            {{ old('send_type') === 'broadcast' ? 'checked' : '' }}>
                                        <span>إرسال جماعي</span>
                                    </label>
                                </div>
                            </div>

                            <div id="individual-fields" class="users-form-card__section">
                                <h6 class="users-form-section-title"><i class="fas fa-user"></i> المستقبل</h6>
                                <div class="users-form-grid">
                                    <div class="users-form-group users-form-group--full">
                                        <label for="student_search" class="users-form-label">البحث عن مستخدم (اختياري)</label>
                                        <select class="users-form-select @error('student_id') is-invalid @enderror"
                                            id="student_search" name="student_id">
                                            <option value="">اختر مستخدماً أو أدخل رقم الهاتف يدوياً</option>
                                        </select>
                                        @error('student_id')
                                            <div class="users-form-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="users-form-group users-form-group--full">
                                        <label for="to" class="users-form-label">
                                            رقم الهاتف
                                            <span class="users-form-required">*</span>
                                        </label>
                                        <input type="text" class="users-form-input @error('to') is-invalid @enderror"
                                            id="to" name="to" value="{{ old('to') }}"
                                            placeholder="+905519665883" dir="ltr">
                                        <span class="users-form-hint">يبدأ بـ + متبوعاً برمز الدولة</span>
                                        @error('to')
                                            <div class="users-form-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div id="individual-placeholders-info" class="email-form-alert" hidden>
                                    <i class="fas fa-info-circle"></i>
                                    <span>
                                        متغيرات متاحة: <code>{student_name}</code>، <code>{student_email}</code>
                                    </span>
                                </div>
                            </div>

                            <div id="broadcast-fields" class="users-form-card__section" hidden>
                                <div class="email-form-alert">
                                    <i class="fas fa-users"></i>
                                    <span>
                                        سيتم الإرسال إلى <strong id="students-count">0</strong> مستخدم لديهم أرقام هواتف صحيحة.
                                    </span>
                                </div>
                            </div>

                            <div class="users-form-card__section">
                                <h6 class="users-form-section-title"><i class="fas fa-comment"></i> محتوى الرسالة</h6>
                                <div class="users-form-grid">
                                    <div class="users-form-group users-form-group--full">
                                        <label for="type" class="users-form-label">
                                            نوع الرسالة
                                            <span class="users-form-required">*</span>
                                        </label>
                                        <select class="users-form-select @error('type') is-invalid @enderror"
                                            id="type" name="type" required>
                                            <option value="text" {{ old('type', 'text') === 'text' ? 'selected' : '' }}>نص</option>
                                            <option value="template" {{ old('type') === 'template' ? 'selected' : '' }}>قالب</option>
                                        </select>
                                        @error('type')
                                            <div class="users-form-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="users-form-group users-form-group--full" id="message-field">
                                        <label for="message" class="users-form-label">
                                            نص الرسالة
                                            <span class="users-form-required">*</span>
                                        </label>
                                        <textarea class="users-form-input @error('message') is-invalid @enderror"
                                            id="message" name="message" rows="5">{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="users-form-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div id="placeholders-info" class="users-form-group users-form-group--full email-form-alert" hidden>
                                        <i class="fas fa-info-circle"></i>
                                        <span>
                                            متغيرات الإرسال الجماعي: <code>{student_name}</code>، <code>{student_email}</code>
                                        </span>
                                    </div>

                                    <div id="template-fields" class="users-form-group users-form-group--full" hidden>
                                        <div class="users-form-grid">
                                            <div class="users-form-group">
                                                <label for="template_name" class="users-form-label">اسم القالب</label>
                                                <input type="text" class="users-form-input @error('template_name') is-invalid @enderror"
                                                    id="template_name" name="template_name" value="{{ old('template_name') }}"
                                                    placeholder="اسم القالب في Meta" dir="ltr">
                                                @error('template_name')
                                                    <div class="users-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="users-form-group">
                                                <label for="language" class="users-form-label">رمز اللغة</label>
                                                <select class="users-form-select @error('language') is-invalid @enderror"
                                                    id="language" name="language">
                                                    <option value="ar" {{ old('language', 'ar') === 'ar' ? 'selected' : '' }}>العربية (ar)</option>
                                                    <option value="en" {{ old('language') === 'en' ? 'selected' : '' }}>English (en)</option>
                                                    <option value="fr" {{ old('language') === 'fr' ? 'selected' : '' }}>Français (fr)</option>
                                                    <option value="es" {{ old('language') === 'es' ? 'selected' : '' }}>Español (es)</option>
                                                </select>
                                                @error('language')
                                                    <div class="users-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit">
                                    <i class="fas fa-paper-plane"></i>
                                    إرسال
                                </button>
                                <a href="{{ route('admin.whatsapp-messages.index') }}" class="users-btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/whatsapp-send-form.js') }}"></script>
    <script>
        WhatsAppSendForm.init({
            searchStudentsUrl: @json(route('admin.whatsapp-messages.search-students')),
            studentsCountUrl: @json(route('admin.whatsapp-messages.broadcast.students-count')),
        });
    </script>
@stop
