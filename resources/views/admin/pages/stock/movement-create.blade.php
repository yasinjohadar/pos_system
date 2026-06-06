@extends('admin.layouts.master')

@section('page-title')
    حركة مخزون جديدة
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
                    <h5 class="users-page-title">حركة مخزون جديدة</h5>
                    <a href="{{ route('admin.stock.movements.index') }}" class="users-btn-secondary">
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
                        'icon' => 'fa-dolly',
                        'title' => 'تسجيل حركة مخزون',
                        'text' => 'أدخل أو صرف أو سوِّ الرصيد يدوياً مع تحديث الأرصدة تلقائياً.',
                        'tips' => ['إدخال يزيد الرصيد', 'صرف ينقص الرصيد', 'التسوية تقبل فرقاً موجباً أو سالباً'],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-exchange-alt"></i> بيانات الحركة</h6>
                        </div>
                        <form action="{{ route('admin.stock.movements.store') }}" method="POST" class="users-form-card__body">
                            @csrf
                            <div class="users-form-grid">
                                <div class="users-form-group users-form-group--full">
                                    <label for="type" class="users-form-label"><i class="fas fa-tag"></i> نوع الحركة <span class="users-form-required">*</span></label>
                                    <select class="users-form-select" id="type" name="type" required>
                                        <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>إدخال</option>
                                        <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>صرف</option>
                                        <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>تسوية</option>
                                    </select>
                                </div>
                                <div class="users-form-group users-form-group--full">
                                    <label for="product_id" class="users-form-label"><i class="fas fa-box"></i> المنتج <span class="users-form-required">*</span></label>
                                    @include('admin.components.premium.product-select', [
                                        'name' => 'product_id',
                                        'id' => 'product_id',
                                        'selected' => $selectedProduct ?? null,
                                    ])
                                </div>
                                <div class="users-form-group users-form-group--full">
                                    <label for="warehouse_id" class="users-form-label"><i class="fas fa-warehouse"></i> المخزن <span class="users-form-required">*</span></label>
                                    <select class="users-form-select" id="warehouse_id" name="warehouse_id" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach ($warehouses as $w)
                                            <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="users-form-group">
                                    <label for="quantity" class="users-form-label"><i class="fas fa-calculator"></i> الكمية <span class="users-form-required">*</span></label>
                                    <input type="number" step="0.0001" min="0" class="users-form-input" id="quantity" name="quantity" value="{{ old('quantity') }}" required>
                                </div>
                                <div class="users-form-group">
                                    <label for="movement_date" class="users-form-label"><i class="fas fa-calendar"></i> التاريخ <span class="users-form-required">*</span></label>
                                    <input type="date" class="users-form-input" id="movement_date" name="movement_date" value="{{ old('movement_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="users-form-group users-form-group--full">
                                    <label for="notes" class="users-form-label"><i class="fas fa-sticky-note"></i> ملاحظات</label>
                                    <textarea class="users-form-textarea" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> حفظ الحركة</button>
                                <a href="{{ route('admin.stock.movements.index') }}" class="users-btn-secondary">إلغاء</a>
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
        AdminPremium.initProductSearch({
            url: '{{ route('admin.products.search-select') }}',
            selector: '#product_id',
        });
    </script>
@stop
