@extends('admin.layouts.master')

@section('page-title')
    تعديل المنتج
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تعديل المنتج: {{ $product->name }}</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.products.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

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

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-pen-to-square',
                        'title' => 'تعديل بيانات المنتج',
                        'text' => 'حدّث بيانات المنتج والأسعار والباركودات الإضافية من مكان واحد.',
                        'tips' => [
                            'الأسعار الإضافية تُحدَّد حسب الفرع ونوع السعر',
                            'يمكن إضافة باركودات متعددة للبحث السريع',
                            'إيقاف التفعيل يخفي المنتج من نقطة البيع',
                        ],
                    ])

                    <div>
                        <div class="users-form-card">
                            <div class="users-form-card__header">
                                <h6 class="users-form-card__title">
                                    <i class="fas fa-box"></i>
                                    بيانات المنتج
                                </h6>
                            </div>
                            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="users-form-card__body">
                                @csrf
                                @method('PUT')

                                @include('admin.pages.products.partials.form-fields', [
                                    'categories' => $categories,
                                    'units' => $units,
                                    'taxes' => $taxes,
                                    'product' => $product,
                                ])

                                <h6 class="users-form-label" style="margin: 1rem 0 0.75rem;">
                                    <i class="fas fa-money-bill-wave"></i>
                                    أسعار إضافية (حسب الفرع ونوع السعر)
                                </h6>
                                <div class="table-responsive mb-3">
                                    <table class="users-table" id="prices-table">
                                        <thead>
                                            <tr>
                                                <th>الفرع</th>
                                                <th>نوع السعر</th>
                                                <th>القيمة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $pricesData = old('prices', $product->prices->count() ? $product->prices : [['branch_id' => null, 'price_type' => 'retail', 'value' => '']]);
                                            @endphp
                                            @foreach ($pricesData as $i => $priceRow)
                                                @php
                                                    $branchId = is_object($priceRow) ? $priceRow->branch_id : ($priceRow['branch_id'] ?? '');
                                                    $priceType = is_object($priceRow) ? $priceRow->price_type : ($priceRow['price_type'] ?? 'retail');
                                                    $value = is_object($priceRow) ? $priceRow->value : ($priceRow['value'] ?? '');
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <select class="users-form-select" name="prices[{{ $i }}][branch_id]">
                                                            <option value="">افتراضي (جميع الفروع)</option>
                                                            @foreach ($branches as $b)
                                                                <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="users-form-select" name="prices[{{ $i }}][price_type]">
                                                            @foreach (\App\Models\ProductPrice::PRICE_TYPES as $k => $v)
                                                                <option value="{{ $k }}" {{ $priceType == $k ? 'selected' : '' }}>{{ $v }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" min="0" class="users-form-input" name="prices[{{ $i }}][value]" value="{{ $value }}" placeholder="0.00">
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr id="price-row-template" style="display:none;">
                                                <td>
                                                    <select class="users-form-select" name="prices[__INDEX__][branch_id]">
                                                        <option value="">افتراضي</option>
                                                        @foreach ($branches as $b)
                                                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="users-form-select" name="prices[__INDEX__][price_type]">
                                                        @foreach (\App\Models\ProductPrice::PRICE_TYPES as $k => $v)
                                                            <option value="{{ $k }}">{{ $v }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" class="users-form-input" name="prices[__INDEX__][value]" placeholder="0.00">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="users-btn-secondary mb-3" id="add-price-row" style="padding: 0.375rem 0.875rem; font-size: 0.8125rem;">
                                    <i class="fas fa-plus"></i>
                                    إضافة سعر
                                </button>

                                <div class="users-form-actions">
                                    <button type="submit" class="users-btn-submit">
                                        <i class="fas fa-save"></i>
                                        حفظ التعديلات
                                    </button>
                                    <a href="{{ route('admin.products.index') }}" class="users-btn-secondary">إلغاء</a>
                                </div>
                            </form>
                        </div>

                        <div class="users-table-card mt-3">
                            <div class="users-detail-card__header">
                                <h6 class="users-detail-card__title">
                                    <i class="fas fa-barcode"></i>
                                    باركودات إضافية
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="users-table">
                                    <thead>
                                        <tr>
                                            <th>الباركود</th>
                                            <th>وصف</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($product->barcodes as $pb)
                                            <tr>
                                                <td dir="ltr">{{ $pb->barcode }}</td>
                                                <td>{{ $pb->description ?? '—' }}</td>
                                                <td>
                                                    <button type="button" class="users-action-btn users-action-btn--delete"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteConfirmModal"
                                                        data-delete-action="{{ route('admin.product-barcodes.destroy', $pb) }}"
                                                        data-delete-title="حذف الباركود"
                                                        data-delete-message="هل أنت متأكد من حذف هذا الباركود؟"
                                                        data-delete-item="{{ $pb->barcode }}"
                                                        title="حذف الباركود">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="users-empty">لا توجد باركودات إضافية</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="users-form-card__body" style="border-top: 1px solid var(--users-border);">
                                <form action="{{ route('admin.products.barcodes.store', $product) }}" method="POST" class="users-filters-form">
                                    @csrf
                                    <input type="text" name="barcode" class="users-search-input" placeholder="باركود جديد" required maxlength="100" dir="ltr">
                                    <input type="text" name="description" class="users-search-input" placeholder="وصف (اختياري)" maxlength="255">
                                    <button type="submit" class="users-btn-filter users-btn-filter--search">
                                        <i class="fas fa-plus"></i>
                                        إضافة
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.components.delete-confirm-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        AdminPremium.initFormToggles();

        document.getElementById('add-price-row')?.addEventListener('click', function () {
            var tbody = document.querySelector('#prices-table tbody');
            var template = document.getElementById('price-row-template');
            if (!tbody || !template) return;
            var index = tbody.querySelectorAll('tr:not(#price-row-template)').length;
            var html = template.outerHTML
                .replace(/__INDEX__/g, index)
                .replace('style="display:none;"', '')
                .replace('id="price-row-template"', '');
            template.insertAdjacentHTML('beforebegin', html);
        });
    </script>
@stop
