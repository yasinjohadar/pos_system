@extends('admin.layouts.master')

@section('page-title')
    تعديل نقاط عميل
@stop

@section('css')
    @include('admin.components.premium.styles')
    @include('admin.components.premium.product-select-assets')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تعديل نقاط عميل</h5>
                    <a href="{{ route('admin.loyalty.index') }}" class="users-btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>

                @include('admin.components.premium.flash')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-star',
                        'title' => 'تعديل نقاط الولاء',
                        'text' => 'أضف أو اخصم نقاطاً يدوياً مع تسجيل السبب في سجل الحركات.',
                        'tips' => ['قيمة موجبة = إضافة نقاط', 'قيمة سالبة = خصم نقاط', 'السبب إلزامي للمراجعة'],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-coins"></i> بيانات التعديل</h6>
                        </div>
                        <form action="{{ route('admin.loyalty.adjust') }}" method="POST" class="users-form-card__body">
                            @csrf
                            <div class="users-form-grid">
                                <div class="users-form-group users-form-group--full">
                                    <label for="customer_id" class="users-form-label"><i class="fas fa-user"></i> العميل <span class="users-form-required">*</span></label>
                                    @include('admin.components.premium.customer-select', [
                                        'selected' => $selectedCustomer ?? null,
                                    ])
                                    <div id="loyalty-balance-hint" class="users-muted-text" style="margin-top: 0.35rem; font-size: 0.8125rem; {{ ($selectedCustomer ?? null) ? '' : 'display:none;' }}">
                                        الرصيد الحالي: <strong id="loyalty-balance-value">{{ optional($selectedCustomer)->loyalty_points ?? 0 }}</strong> نقطة
                                    </div>
                                </div>
                                <div class="users-form-group users-form-group--full">
                                    <label for="points" class="users-form-label"><i class="fas fa-plus-minus"></i> عدد النقاط <span class="users-form-required">*</span></label>
                                    <input type="number" name="points" id="points" class="users-form-input @error('points') is-invalid @enderror" value="{{ old('points') }}" required placeholder="مثال: 50 أو -20">
                                    <p class="users-muted-text" style="margin-top: 0.35rem; font-size: 0.8125rem;">موجب للإضافة، سالب للخصم</p>
                                    @error('points')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="users-form-group users-form-group--full">
                                    <label for="reason" class="users-form-label"><i class="fas fa-comment-alt"></i> السبب / الوصف <span class="users-form-required">*</span></label>
                                    <textarea name="reason" id="reason" class="users-form-textarea @error('reason') is-invalid @enderror" rows="3" required placeholder="مثال: تصحيح رصيد، مكافأة، إلخ">{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit"><i class="fas fa-check"></i> تطبيق التعديل</button>
                                <a href="{{ route('admin.loyalty.index') }}" class="users-btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.product-select-scripts')
    @include('admin.components.premium.scripts')
    <script>
        AdminPremium.initCustomerSearch({
            url: '{{ route('admin.customers.search-select') }}',
            selector: '#customer_id',
            minimumInputLength: 0,
            onSelect: function (data) {
                var hint = document.getElementById('loyalty-balance-hint');
                var valueEl = document.getElementById('loyalty-balance-value');
                if (!data || !data.id) {
                    hint.style.display = 'none';
                    return;
                }
                hint.style.display = '';
                valueEl.textContent = data.loyalty_points != null ? data.loyalty_points : 0;
            },
        });
    </script>
@stop
