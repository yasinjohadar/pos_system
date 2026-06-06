@extends('admin.layouts.master')

@section('page-title')
    أمر شراء جديد
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
                    <h5 class="users-page-title">أمر شراء جديد</h5>
                    <a href="{{ route('admin.purchase-orders.index') }}" class="users-btn-secondary">
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

                <form action="{{ route('admin.purchase-orders.store') }}" method="POST">
                    @csrf
                    <div class="users-form-layout">
                        @include('admin.components.premium.form-aside', [
                            'icon' => 'fa-shopping-cart',
                            'title' => 'أمر شراء',
                            'text' => 'أنشئ أمر شراء للمورد قبل استلام البضاعة.',
                            'tips' => ['حدّد المورد والمخزن', 'يمكن تحويل الأمر لاحقاً إلى فاتورة شراء'],
                        ])
                        <div class="users-form-card">
                            <div class="users-form-card__header">
                                <h6 class="users-form-card__title"><i class="fas fa-shopping-cart"></i> بيانات الأمر</h6>
                            </div>
                            <div class="users-form-card__body">
                                <div class="users-form-grid">
                                    <div class="users-form-group">
                                        <label class="users-form-label">تاريخ الأمر</label>
                                        <input type="date" name="order_date" class="users-form-input" value="{{ old('order_date', date('Y-m-d')) }}" required>
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">تاريخ التسليم المتوقع</label>
                                        <input type="date" name="expected_date" class="users-form-input" value="{{ old('expected_date') }}">
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">الفرع</label>
                                        <select name="branch_id" class="users-form-select" required>
                                            @foreach ($branches as $b)
                                                <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">المخزن</label>
                                        <select name="warehouse_id" class="users-form-select">
                                            <option value="">—</option>
                                            @foreach ($warehouses as $w)
                                                <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">المورد</label>
                                        @include('admin.components.premium.supplier-select', ['required' => false])
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">نسبة الضريبة %</label>
                                        <input type="number" name="tax_rate" class="users-form-input" step="0.01" min="0" max="100" value="{{ old('tax_rate', 0) }}">
                                    </div>
                                    <div class="users-form-group users-form-group--full">
                                        <label class="users-form-label">ملاحظات</label>
                                        <textarea name="notes" class="users-form-input" rows="2">{{ old('notes') }}</textarea>
                                    </div>
                                </div>

                                <div class="users-table-card" style="margin-top: 1rem;">
                                    <div class="users-form-card__header" style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--users-border); display: flex; justify-content: space-between;">
                                        <h6 class="users-form-card__title" style="margin: 0;">البنود</h6>
                                        <button type="button" class="users-btn-secondary" id="po-add-line" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;"><i class="fas fa-plus"></i> إضافة</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="users-table">
                                            <thead><tr><th>المنتج</th><th>الكمية</th><th>السعر</th><th></th></tr></thead>
                                            <tbody id="po-lines-body">
                                                <tr class="po-line-row">
                                                    <td>@include('admin.components.premium.product-select', ['name' => 'items[0][product_id]', 'id' => 'items_0_product_id'])</td>
                                                    <td><input type="number" name="items[0][quantity]" class="users-form-input" step="any" min="0.0001" value="1" required></td>
                                                    <td><input type="number" name="items[0][unit_price]" class="users-form-input" step="0.01" min="0" value="0" required></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="users-form-actions">
                                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> حفظ الأمر</button>
                                    <a href="{{ route('admin.purchase-orders.index') }}" class="users-btn-secondary">إلغاء</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <template id="po-line-template">
        <tr class="po-line-row">
            <td><select name="items[__INDEX__][product_id]" class="users-form-select users-product-search" data-placeholder="ابحث..." required><option value=""></option></select></td>
            <td><input type="number" name="items[__INDEX__][quantity]" class="users-form-input" step="any" min="0.0001" value="1" required></td>
            <td><input type="number" name="items[__INDEX__][unit_price]" class="users-form-input" step="0.01" min="0" value="0" required></td>
            <td><button type="button" class="users-action-btn users-action-btn--delete po-remove-line"><i class="fa-solid fa-trash-can"></i></button></td>
        </tr>
    </template>
@stop

@section('script')
    @include('admin.components.premium.product-select-scripts')
    @include('admin.components.premium.scripts')
    <script>
        (function () {
            var idx = 1;
            document.getElementById('po-add-line').addEventListener('click', function () {
                var html = document.getElementById('po-line-template').innerHTML.replace(/__INDEX__/g, idx++);
                document.getElementById('po-lines-body').insertAdjacentHTML('beforeend', html);
                if (typeof AdminPremium !== 'undefined' && AdminPremium.initProductSearch) AdminPremium.initProductSearch();
            });
            document.getElementById('po-lines-body').addEventListener('click', function (e) {
                if (e.target.closest('.po-remove-line')) {
                    if (document.querySelectorAll('.po-line-row').length <= 1) return;
                    e.target.closest('.po-line-row').remove();
                }
            });
        })();
    </script>
@stop
