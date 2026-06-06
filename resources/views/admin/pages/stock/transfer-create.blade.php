@extends('admin.layouts.master')

@section('page-title')
    تحويل مخزون جديد
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
                    <h5 class="users-page-title">تحويل مخزون جديد</h5>
                    <a href="{{ route('admin.stock.transfers.index') }}" class="users-btn-secondary">
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
                        'icon' => 'fa-exchange-alt',
                        'title' => 'تحويل بين المخازن',
                        'text' => 'انقل كميات منتجات من مخزن إلى آخر مع تسجيل حركات خروج ودخول تلقائياً.',
                        'tips' => ['تأكد من توفر الرصيد في المخزن المصدر', 'يمكن إضافة عدة بنود في تحويل واحد'],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-truck-loading"></i> بيانات التحويل</h6>
                        </div>
                        <form action="{{ route('admin.stock.transfers.store') }}" method="POST" id="transfer-form" class="users-form-card__body">
                            @csrf
                            <div class="users-form-grid">
                                <div class="users-form-group">
                                    <label for="from_warehouse_id" class="users-form-label"><i class="fas fa-warehouse"></i> من مخزن <span class="users-form-required">*</span></label>
                                    <select class="users-form-select" id="from_warehouse_id" name="from_warehouse_id" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach ($warehouses as $w)
                                            <option value="{{ $w->id }}" {{ old('from_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="users-form-group">
                                    <label for="to_warehouse_id" class="users-form-label"><i class="fas fa-warehouse"></i> إلى مخزن <span class="users-form-required">*</span></label>
                                    <select class="users-form-select" id="to_warehouse_id" name="to_warehouse_id" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach ($warehouses as $w)
                                            <option value="{{ $w->id }}" {{ old('to_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="users-form-group">
                                    <label for="transfer_date" class="users-form-label"><i class="fas fa-calendar"></i> التاريخ <span class="users-form-required">*</span></label>
                                    <input type="date" class="users-form-input" id="transfer_date" name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="users-form-group users-form-group--full">
                                    <label for="notes" class="users-form-label"><i class="fas fa-sticky-note"></i> ملاحظات</label>
                                    <textarea class="users-form-textarea" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <h6 class="users-form-section-title"><i class="fas fa-list"></i> بنود التحويل</h6>
                            <div class="users-table-card users-table-card--nested">
                                <div class="table-responsive">
                                    <table class="users-table" id="items-table">
                                        <thead>
                                            <tr>
                                                <th>المنتج</th>
                                                <th style="width: 140px;">الكمية</th>
                                                <th style="width: 60px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $oldItems = old('items', []); @endphp
                                            @forelse ($oldItems as $i => $item)
                                                <tr>
                                                    <td>
                                                        @include('admin.components.premium.product-select', [
                                                            'name' => 'items[' . $i . '][product_id]',
                                                            'id' => 'transfer_product_' . $i,
                                                            'selected' => isset($item['product_id'], $oldProducts[$item['product_id']]) ? $oldProducts[$item['product_id']] : null,
                                                        ])
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.0001" min="0.0001" class="users-form-input" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? '' }}" required>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="users-action-btn users-action-btn--delete remove-row" title="حذف"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr class="item-row">
                                                    <td>
                                                        @include('admin.components.premium.product-select', [
                                                            'name' => 'items[0][product_id]',
                                                            'id' => 'transfer_product_0',
                                                        ])
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.0001" min="0.0001" class="users-form-input" name="items[0][quantity]" required>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="users-action-btn users-action-btn--delete remove-row" title="حذف"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <button type="button" class="users-btn-secondary" id="add-item" style="margin-top: 0.75rem;">
                                <i class="fas fa-plus"></i> إضافة بند
                            </button>

                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit"><i class="fas fa-exchange-alt"></i> تنفيذ التحويل</button>
                                <a href="{{ route('admin.stock.transfers.index') }}" class="users-btn-secondary">إلغاء</a>
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
        (function () {
            var searchUrl = '{{ route('admin.products.search-select') }}';
            var rowIndex = {{ max(count(old('items', [])), 1) }};

            function initRowProductSearch(row) {
                var select = row.querySelector('.users-product-search');
                if (!select) {
                    return;
                }
                AdminPremium.initProductSearch({
                    url: searchUrl,
                    selector: '#' + select.id,
                });
            }

            document.querySelectorAll('#items-table tbody tr').forEach(initRowProductSearch);

            document.getElementById('add-item')?.addEventListener('click', function () {
                var tbody = document.querySelector('#items-table tbody');
                var tr = document.createElement('tr');
                var selectId = 'transfer_product_' + rowIndex;
                tr.innerHTML =
                    '<td>' +
                        '<select name="items[' + rowIndex + '][product_id]" id="' + selectId + '" class="users-form-select users-product-search" data-placeholder="ابحث بالاسم أو الباركود..." required>' +
                            '<option value=""></option>' +
                        '</select>' +
                    '</td>' +
                    '<td><input type="number" step="0.0001" min="0.0001" class="users-form-input" name="items[' + rowIndex + '][quantity]" required></td>' +
                    '<td><button type="button" class="users-action-btn users-action-btn--delete remove-row" title="حذف"><i class="fas fa-trash"></i></button></td>';
                tbody.appendChild(tr);
                initRowProductSearch(tr);
                rowIndex++;
            });

            document.getElementById('items-table')?.addEventListener('click', function (e) {
                if (e.target.closest('.remove-row')) {
                    var row = e.target.closest('tr');
                    if (document.querySelectorAll('#items-table tbody tr').length > 1) {
                        var select = row.querySelector('.users-product-search');
                        if (select && jQuery(select).data('select2')) {
                            jQuery(select).select2('destroy');
                        }
                        row.remove();
                    }
                }
            });
        })();
    </script>
@stop
