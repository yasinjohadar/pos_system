@php
    $promotion = $promotion ?? null;
    $oldItems = old('items');
    if ($oldItems === null && $promotion) {
        $oldItems = $promotion->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'max_qty' => $item->max_qty,
        ])->toArray();
    }
    if (empty($oldItems)) {
        $oldItems = [[]];
    }
@endphp

<form action="{{ $action }}" method="POST" id="promotion-form">
    @csrf
    @if (!empty($method) && $method !== 'POST')
        @method($method)
    @endif

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-tags',
            'title' => $promotion ? 'تعديل عرض ترويجي' : 'عرض ترويجي جديد',
            'text' => 'حدّد نوع الخصم والفترة والمنتجات المشمولة. يُطبَّق العرض تلقائياً عند استيفاء الشروط.',
            'tips' => ['نسبة مئوية أو مبلغ ثابت', 'يمكن تحديد حد أدنى للفاتورة أو الكمية', 'اربط منتجات محددة بالعرض'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-percent"></i> بيانات العرض</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group users-form-group--full">
                        <label for="name" class="users-form-label"><i class="fas fa-tag"></i> اسم العرض <span class="users-form-required">*</span></label>
                        <input type="text" name="name" id="name" class="users-form-input" value="{{ old('name', $promotion->name ?? '') }}" required>
                    </div>
                    <div class="users-form-group">
                        <label for="type" class="users-form-label"><i class="fas fa-sliders-h"></i> نوع الخصم <span class="users-form-required">*</span></label>
                        <select name="type" id="type" class="users-form-select" required>
                            <option value="percent" {{ old('type', $promotion->type ?? 'percent') === 'percent' ? 'selected' : '' }}>نسبة مئوية</option>
                            <option value="fixed" {{ old('type', $promotion->type ?? '') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                        </select>
                    </div>
                    <div class="users-form-group">
                        <label for="value" class="users-form-label"><i class="fas fa-calculator"></i> قيمة الخصم <span class="users-form-required">*</span></label>
                        <input type="number" step="0.01" min="0" name="value" id="value" class="users-form-input" value="{{ old('value', $promotion->value ?? 0) }}" required>
                    </div>
                    <div class="users-form-group">
                        <label for="start_date" class="users-form-label"><i class="fas fa-calendar-alt"></i> تاريخ البداية</label>
                        <input type="date" name="start_date" id="start_date" class="users-form-input" value="{{ old('start_date', optional($promotion)->start_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="users-form-group">
                        <label for="end_date" class="users-form-label"><i class="fas fa-calendar-check"></i> تاريخ النهاية</label>
                        <input type="date" name="end_date" id="end_date" class="users-form-input" value="{{ old('end_date', optional($promotion)->end_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="users-form-group">
                        <label for="min_invoice_amount" class="users-form-label"><i class="fas fa-file-invoice-dollar"></i> الحد الأدنى للفاتورة</label>
                        <input type="number" step="0.01" min="0" name="min_invoice_amount" id="min_invoice_amount" class="users-form-input" value="{{ old('min_invoice_amount', $promotion->min_invoice_amount ?? '') }}">
                    </div>
                    <div class="users-form-group">
                        <label for="min_qty" class="users-form-label"><i class="fas fa-boxes"></i> الحد الأدنى للكمية</label>
                        <input type="number" step="0.01" min="0" name="min_qty" id="min_qty" class="users-form-input" value="{{ old('min_qty', $promotion->min_qty ?? '') }}">
                    </div>
                    <div class="users-form-group users-form-group--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" name="is_active" value="1" class="users-form-toggle-input" {{ old('is_active', $promotion->is_active ?? true) ? 'checked' : '' }}>
                            <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                            <span class="users-form-toggle-label">العرض نشط</span>
                        </label>
                    </div>
                </div>

                <h6 class="users-form-section-title"><i class="fas fa-box"></i> المنتجات المشمولة بالعرض</h6>
                <div class="users-table-card users-table-card--nested">
                    <div class="table-responsive">
                        <table class="users-table" id="promotion-items-table">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th style="width: 180px;">أقصى كمية (اختياري)</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody id="promotion-items-body">
                                @foreach ($oldItems as $index => $row)
                                    <tr class="promotion-item-row">
                                        <td>
                                            @include('admin.components.premium.product-select', [
                                                'name' => 'items[' . $index . '][product_id]',
                                                'id' => 'promotion_product_' . $index,
                                                'selected' => isset($row['product_id'], $oldProducts[$row['product_id']]) ? $oldProducts[$row['product_id']] : null,
                                                'required' => false,
                                            ])
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $index }}][max_qty]" step="0.01" min="0" class="users-form-input" value="{{ $row['max_qty'] ?? '' }}">
                                        </td>
                                        <td>
                                            <button type="button" class="users-action-btn users-action-btn--delete promotion-remove-row" title="حذف"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <button type="button" class="users-btn-secondary" id="promotion-add-row" style="margin-top: 0.75rem;">
                    <i class="fas fa-plus"></i> إضافة منتج آخر
                </button>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ العرض' }}</button>
                    <a href="{{ route('admin.promotions.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
