@extends('admin.layouts.master')

@section('page-title')
    {{ $saleInvoice ? 'مرتجع من فاتورة ' . $saleInvoice->number : 'مرتجع بيع جديد' }}
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
                    <h5 class="users-page-title">
                        @if ($saleInvoice)
                            مرتجع من فاتورة: {{ $saleInvoice->number }}
                        @else
                            مرتجع بيع جديد
                        @endif
                    </h5>
                    <a href="{{ $saleInvoice ? route('admin.sale-invoices.show', $saleInvoice) : route('admin.sale-returns.index') }}" class="users-btn-secondary">
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

                <form action="{{ route('admin.sale-returns.store') }}" method="POST" id="sale-return-form"
                    data-invoice-data-url="{{ url('admin/sale-returns/invoice-data') }}">
                    @csrf

                    <div class="users-form-layout">
                        @include('admin.components.premium.form-aside', [
                            'icon' => 'fa-undo',
                            'title' => 'تسجيل مرتجع بيع',
                            'text' => 'اختر الفاتورة المؤكدة ثم حدد الكميات المرتجعة. يُحفظ المرتجع بحالة انتظار حتى تكمله لإدخال المخزون.',
                            'tips' => ['يمكن أيضاً فتح المرتجع من صفحة الفاتورة', 'الكمية لا تتجاوز المتبقي القابل للمرتجع'],
                        ])

                        <div class="users-form-card">
                            <div class="users-form-card__header">
                                <h6 class="users-form-card__title"><i class="fas fa-file-invoice"></i> الفاتورة وبيانات المرتجع</h6>
                            </div>
                            <div class="users-form-card__body">
                                @if ($saleInvoice)
                                    <input type="hidden" name="sale_invoice_id" id="sale_invoice_id" value="{{ $saleInvoice->id }}">
                                    <div class="users-form-group users-form-group--full">
                                        <label class="users-form-label"><i class="fas fa-receipt"></i> الفاتورة الأصلية</label>
                                        <div class="users-badge users-badge--role" style="font-size: 0.875rem; padding: 0.5rem 0.75rem;">
                                            {{ $saleInvoice->number }} — {{ $saleInvoice->branch->name ?? '—' }} — {{ $saleInvoice->invoice_date->format('Y-m-d') }}
                                        </div>
                                    </div>
                                @else
                                    <div class="users-form-group users-form-group--full">
                                        <label for="sale_invoice_id" class="users-form-label"><i class="fas fa-receipt"></i> الفاتورة الأصلية <span class="users-form-required">*</span></label>
                                        <select name="sale_invoice_id" id="sale_invoice_id" class="users-form-select users-invoice-search" required data-placeholder="ابحث برقم الفاتورة...">
                                            <option value=""></option>
                                            @if (old('sale_invoice_id'))
                                                @php $oldInv = \App\Models\SaleInvoice::with('branch')->find(old('sale_invoice_id')); @endphp
                                                @if ($oldInv)
                                                    <option value="{{ $oldInv->id }}" selected>{{ $oldInv->number }} — {{ $oldInv->branch->name ?? '—' }}</option>
                                                @endif
                                            @endif
                                        </select>
                                        <p class="users-muted-text" style="margin-top: 0.35rem; font-size: 0.8125rem;">أو أنشئ المرتجع من صفحة الفاتورة (زر «مرتجع»).</p>
                                    </div>
                                @endif

                                <div class="users-form-grid">
                                    <div class="users-form-group">
                                        <label for="warehouse_id" class="users-form-label"><i class="fas fa-warehouse"></i> مخزن الاستلام <span class="users-form-required">*</span></label>
                                        <select name="warehouse_id" id="warehouse_id" class="users-form-select" required>
                                            <option value="">اختر المخزن</option>
                                            @foreach ($warehouses as $w)
                                                <option value="{{ $w->id }}" {{ old('warehouse_id', $saleInvoice->warehouse_id ?? '') == $w->id ? 'selected' : '' }}>
                                                    {{ $w->name }} ({{ $w->branch->name ?? '—' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="users-form-group">
                                        <label for="return_date" class="users-form-label"><i class="fas fa-calendar"></i> تاريخ المرتجع <span class="users-form-required">*</span></label>
                                        <input type="date" name="return_date" id="return_date" class="users-form-input" value="{{ old('return_date', date('Y-m-d')) }}" required>
                                    </div>
                                    <div class="users-form-group users-form-group--full">
                                        <label for="notes" class="users-form-label"><i class="fas fa-sticky-note"></i> ملاحظات</label>
                                        <textarea name="notes" id="notes" class="users-form-textarea" rows="2">{{ old('notes') }}</textarea>
                                    </div>
                                </div>

                                <h6 class="users-form-section-title"><i class="fas fa-list"></i> بنود المرتجع</h6>
                                <div class="users-table-card users-table-card--nested">
                                    <div class="table-responsive">
                                        <table class="users-table" id="return-items-table">
                                            <thead>
                                                <tr>
                                                    <th>المنتج</th>
                                                    <th style="width: 110px;">الكمية</th>
                                                    <th style="width: 120px;">سعر الوحدة</th>
                                                    <th style="width: 120px;">الإجمالي</th>
                                                </tr>
                                            </thead>
                                            <tbody id="return-items-tbody">
                                                @if ($saleInvoice && $saleInvoice->items->isNotEmpty())
                                                    @foreach ($saleInvoice->items as $i => $item)
                                                        <tr class="return-item-row">
                                                            <td>
                                                                <input type="hidden" name="items[{{ $i }}][sale_invoice_item_id]" value="{{ $item->id }}">
                                                                <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $item->product_id }}">
                                                                <div class="users-user-cell">
                                                                    <div class="users-avatar"><i class="fas fa-box"></i></div>
                                                                    <div>
                                                                        <span class="users-user-name" style="cursor: default;">{{ $item->product->name ?? '—' }}</span>
                                                                        <small class="users-muted-text d-block">قابل للمرتجع: {{ number_format($item->quantity_remaining, 3) }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.001" name="items[{{ $i }}][quantity]" class="users-form-input return-qty" value="0" max="{{ $item->quantity_remaining }}" min="0">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="users-form-input return-price" value="{{ $item->unit_price }}" min="0">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="users-form-input return-line-total" readonly value="0.00">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr id="return-items-empty">
                                                        <td colspan="4" class="users-empty">اختر الفاتورة لتحميل البنود تلقائياً</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="users-form-actions" id="return-form-actions" style="{{ ($saleInvoice && $saleInvoice->items->isNotEmpty()) ? '' : 'display:none;' }}">
                                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> حفظ المرتجع</button>
                                    <a href="{{ $saleInvoice ? route('admin.sale-invoices.show', $saleInvoice) : route('admin.sale-returns.index') }}" class="users-btn-secondary">إلغاء</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.product-select-scripts')
    @include('admin.components.premium.scripts')
    <script>
        (function () {
            var form = document.getElementById('sale-return-form');
            var tbody = document.getElementById('return-items-tbody');
            var warehouseSelect = document.getElementById('warehouse_id');
            var actionsEl = document.getElementById('return-form-actions');
            var invoiceDataBase = form.dataset.invoiceDataUrl;
            var preloadedInvoiceId = @json($saleInvoice?->id);

            function bindRow(row) {
                var qtyInput = row.querySelector('.return-qty');
                var priceInput = row.querySelector('.return-price');
                var totalInput = row.querySelector('.return-line-total');
                if (!qtyInput || !priceInput || !totalInput) return;

                function update() {
                    var q = parseFloat(qtyInput.value) || 0;
                    var p = parseFloat(priceInput.value) || 0;
                    totalInput.value = (q * p).toFixed(2);
                }
                qtyInput.addEventListener('input', update);
                priceInput.addEventListener('input', update);
                update();
            }

            function renderItems(items) {
                tbody.innerHTML = '';
                if (!items || !items.length) {
                    tbody.innerHTML = '<tr><td colspan="4" class="users-empty">لا توجد بنود قابلة للمرتجع في هذه الفاتورة</td></tr>';
                    actionsEl.style.display = 'none';
                    return;
                }

                items.forEach(function (item, i) {
                    if (item.quantity_remaining <= 0) return;

                    var tr = document.createElement('tr');
                    tr.className = 'return-item-row';
                    tr.innerHTML =
                        '<td>' +
                            '<input type="hidden" name="items[' + i + '][sale_invoice_item_id]" value="' + item.sale_invoice_item_id + '">' +
                            '<input type="hidden" name="items[' + i + '][product_id]" value="' + item.product_id + '">' +
                            '<div class="users-user-cell">' +
                                '<div class="users-avatar"><i class="fas fa-box"></i></div>' +
                                '<div>' +
                                    '<span class="users-user-name" style="cursor:default;">' + item.product_name + '</span>' +
                                    '<small class="users-muted-text d-block">قابل للمرتجع: ' + parseFloat(item.quantity_remaining).toFixed(3) + '</small>' +
                                '</div>' +
                            '</div>' +
                        '</td>' +
                        '<td><input type="number" step="0.001" name="items[' + i + '][quantity]" class="users-form-input return-qty" value="0" max="' + item.quantity_remaining + '" min="0"></td>' +
                        '<td><input type="number" step="0.01" name="items[' + i + '][unit_price]" class="users-form-input return-price" value="' + item.unit_price + '" min="0"></td>' +
                        '<td><input type="text" class="users-form-input return-line-total" readonly value="0.00"></td>';
                    tbody.appendChild(tr);
                    bindRow(tr);
                });

                if (!tbody.querySelector('.return-item-row')) {
                    tbody.innerHTML = '<tr><td colspan="4" class="users-empty">لا توجد كميات متبقية للمرتجع</td></tr>';
                    actionsEl.style.display = 'none';
                } else {
                    actionsEl.style.display = '';
                }
            }

            function loadInvoiceData(invoiceId) {
                if (!invoiceId) {
                    tbody.innerHTML = '<tr id="return-items-empty"><td colspan="4" class="users-empty">اختر الفاتورة لتحميل البنود تلقائياً</td></tr>';
                    actionsEl.style.display = 'none';
                    return;
                }

                tbody.innerHTML = '<tr><td colspan="4" class="users-empty"><i class="fas fa-spinner fa-spin me-1"></i> جاري تحميل البنود...</td></tr>';

                fetch(invoiceDataBase + '/' + invoiceId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                    .then(function (r) {
                        if (!r.ok) throw new Error('تعذر تحميل بيانات الفاتورة');
                        return r.json();
                    })
                    .then(function (data) {
                        if (data.warehouse_id && warehouseSelect) {
                            warehouseSelect.value = String(data.warehouse_id);
                        }
                        renderItems(data.items || []);
                    })
                    .catch(function () {
                        tbody.innerHTML = '<tr><td colspan="4" class="users-empty">حدث خطأ أثناء تحميل البنود</td></tr>';
                        actionsEl.style.display = 'none';
                        AdminPremium.showToast('تعذر تحميل بنود الفاتورة', 'error');
                    });
            }

            document.querySelectorAll('.return-item-row').forEach(bindRow);

            var invoiceSelect = document.getElementById('sale_invoice_id');
            if (invoiceSelect && invoiceSelect.tagName === 'SELECT') {
                AdminPremium.initProductSearch({
                    url: '{{ route('admin.sale-returns.search-invoices') }}',
                    selector: '#sale_invoice_id',
                    placeholder: 'ابحث برقم الفاتورة أو المعرف...',
                    minimumInputLength: 0,
                });

                jQuery(invoiceSelect).on('change', function () {
                    loadInvoiceData(this.value);
                });

                if (preloadedInvoiceId) {
                    loadInvoiceData(preloadedInvoiceId);
                } else if (invoiceSelect.value) {
                    loadInvoiceData(invoiceSelect.value);
                }
            }

            form.addEventListener('submit', function () {
                document.querySelectorAll('.return-item-row').forEach(function (row) {
                    var qty = row.querySelector('.return-qty');
                    if (!qty || parseFloat(qty.value) <= 0) {
                        row.querySelectorAll('input, select').forEach(function (inp) {
                            inp.disabled = true;
                        });
                    }
                });
            });
        })();
    </script>
@stop
