@php
    $saleInvoice = $saleInvoice ?? null;
    $oldItems = old('items');
    if ($oldItems === null && $saleInvoice) {
        $oldItems = $saleInvoice->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'warehouse_id' => $item->warehouse_id,
        ])->toArray();
    }
    if (empty($oldItems)) {
        $oldItems = [['quantity' => 1, 'unit_price' => 0]];
    }
@endphp

<form action="{{ $action }}" method="POST" id="invoice-form"
    data-product-price-url="{{ route('admin.sale-invoices.product-price') }}"
    data-product-search-url="{{ route('admin.products.search-select') }}">
    @csrf
    @if (!empty($method) && $method !== 'POST')
        @method($method)
    @endif

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-file-invoice-dollar',
            'title' => $saleInvoice ? 'تعديل فاتورة بيع' : 'فاتورة بيع جديدة',
            'text' => 'أنشئ فاتورة مسودة ثم أكّدها لصرف المخزون. ابحث عن العميل والمنتجات مباشرة دون تحميل القوائم كاملة.',
            'tips' => ['الفاتورة تُحفظ كمسودة حتى التأكيد', 'يُجلب سعر البيع تلقائياً عند اختيار المنتج', 'يمكن تطبيق كوبون عند الحفظ'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-receipt"></i> بيانات الفاتورة</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group">
                        <label for="branch_id" class="users-form-label"><i class="fas fa-building"></i> الفرع <span class="users-form-required">*</span></label>
                        <select name="branch_id" id="branch_id" class="users-form-select @error('branch_id') is-invalid @enderror" required>
                            <option value="">اختر الفرع</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id', optional($saleInvoice)->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="customer_id" class="users-form-label"><i class="fas fa-user"></i> العميل</label>
                        @include('admin.components.premium.customer-select', [
                            'selected' => $selectedCustomer ?? null,
                            'required' => false,
                            'placeholder' => 'عميل نقدي / ابحث بالاسم...',
                        ])
                        @error('customer_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="warehouse_id" class="users-form-label"><i class="fas fa-warehouse"></i> مخزن الصرف <span class="users-form-required">*</span></label>
                        <select name="warehouse_id" id="warehouse_id" class="users-form-select @error('warehouse_id') is-invalid @enderror" required>
                            <option value="">اختر المخزن</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" data-branch="{{ $w->branch_id }}" {{ old('warehouse_id', optional($saleInvoice)->warehouse_id) == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }} ({{ $w->branch->name ?? '—' }})
                                </option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="invoice_date" class="users-form-label"><i class="fas fa-calendar"></i> تاريخ الفاتورة <span class="users-form-required">*</span></label>
                        <input type="date" name="invoice_date" id="invoice_date" class="users-form-input @error('invoice_date') is-invalid @enderror"
                            value="{{ old('invoice_date', optional($saleInvoice)->invoice_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                        @error('invoice_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="tax_rate" class="users-form-label"><i class="fas fa-percent"></i> نسبة الضريبة %</label>
                        <input type="number" step="0.01" name="tax_rate" id="tax_rate" class="users-form-input @error('tax_rate') is-invalid @enderror"
                            value="{{ old('tax_rate', optional($saleInvoice)->tax_rate ?? 0) }}" min="0" max="100">
                        @error('tax_rate')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="discount_type" class="users-form-label"><i class="fas fa-tag"></i> نوع الخصم</label>
                        <select name="discount_type" id="discount_type" class="users-form-select">
                            <option value="">بدون</option>
                            <option value="fixed" {{ old('discount_type', optional($saleInvoice)->discount_type) === 'fixed' ? 'selected' : '' }}>مبلغ</option>
                            <option value="percent" {{ old('discount_type', optional($saleInvoice)->discount_type) === 'percent' ? 'selected' : '' }}>نسبة %</option>
                        </select>
                    </div>

                    <div class="users-form-group">
                        <label for="discount_value" class="users-form-label"><i class="fas fa-calculator"></i> قيمة الخصم</label>
                        <input type="number" step="0.01" name="discount_value" id="discount_value" class="users-form-input"
                            value="{{ old('discount_value', optional($saleInvoice)->discount_value ?? 0) }}" min="0">
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label for="coupon_code" class="users-form-label"><i class="fas fa-ticket-alt"></i> كود الخصم (كوبون)</label>
                        <input type="text" name="coupon_code" id="coupon_code" class="users-form-input"
                            value="{{ old('coupon_code', optional(optional($saleInvoice)->coupon)->code) }}" placeholder="أدخل كود الكوبون ثم احفظ الفاتورة">
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label for="notes" class="users-form-label"><i class="fas fa-sticky-note"></i> ملاحظات</label>
                        <textarea name="notes" id="notes" class="users-form-textarea" rows="2">{{ old('notes', optional($saleInvoice)->notes) }}</textarea>
                    </div>
                </div>

                <h6 class="users-form-section-title"><i class="fas fa-list"></i> بنود الفاتورة</h6>
                <div class="users-table-card users-table-card--nested">
                    <div class="table-responsive">
                        <table class="users-table" id="items-table">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th style="width: 110px;">الكمية</th>
                                    <th style="width: 130px;">سعر الوحدة</th>
                                    <th style="width: 130px;">الإجمالي</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">
                                @foreach ($oldItems as $index => $row)
                                    <tr class="invoice-item-row">
                                        <td>
                                            @include('admin.components.premium.product-select', [
                                                'name' => 'items[' . $index . '][product_id]',
                                                'id' => 'invoice_product_' . $index,
                                                'selected' => isset($row['product_id'], $oldProducts[$row['product_id']]) ? $oldProducts[$row['product_id']] : null,
                                                'required' => true,
                                            ])
                                            <input type="hidden" name="items[{{ $index }}][warehouse_id]" value="{{ $row['warehouse_id'] ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.001" name="items[{{ $index }}][quantity]" class="users-form-input invoice-qty"
                                                value="{{ $row['quantity'] ?? 1 }}" min="0.001">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="users-form-input invoice-price"
                                                value="{{ $row['unit_price'] ?? 0 }}" min="0">
                                        </td>
                                        <td>
                                            <input type="text" class="users-form-input invoice-line-total" readonly value="0.00">
                                        </td>
                                        <td>
                                            <button type="button" class="users-action-btn users-action-btn--delete invoice-remove-row" title="حذف"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <button type="button" class="users-btn-secondary" id="add-item-row" style="margin-top: 0.75rem;">
                    <i class="fas fa-plus"></i> إضافة بند
                </button>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ الفاتورة (مسودة)' }}</button>
                    <a href="{{ $cancelUrl }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
