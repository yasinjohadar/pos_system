@php
    $priceList = $priceList ?? null;
    $oldItems = old('items');
    if ($oldItems === null && $priceList) {
        $oldItems = $priceList->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'price' => $item->price,
        ])->toArray();
    }
    if (empty($oldItems)) {
        $oldItems = [[]];
    }
@endphp

<form action="{{ $action }}" method="POST" id="price-list-form">
    @csrf
    @if (!empty($method) && $method !== 'POST')
        @method($method)
    @endif

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-list-alt',
            'title' => $priceList ? 'تعديل قائمة أسعار' : 'قائمة أسعار جديدة',
            'text' => 'عرّف أسعاراً خاصة لمجموعة منتجات واربطها بالعملاء لتطبيقها تلقائياً في الفواتير.',
            'tips' => ['اسم واضح مثل «أسعار الجملة»', 'ابحث عن المنتج بالاسم أو الباركود', 'يمكن إضافة عدة منتجات بأسعار مختلفة'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-tags"></i> بيانات القائمة</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group">
                        <label for="name" class="users-form-label"><i class="fas fa-tag"></i> اسم القائمة <span class="users-form-required">*</span></label>
                        <input type="text" name="name" id="name" class="users-form-input" value="{{ old('name', $priceList->name ?? '') }}" required>
                    </div>
                    <div class="users-form-group">
                        <label for="description" class="users-form-label"><i class="fas fa-align-right"></i> الوصف</label>
                        <input type="text" name="description" id="description" class="users-form-input" value="{{ old('description', $priceList->description ?? '') }}">
                    </div>
                    <div class="users-form-group users-form-group--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" name="is_active" value="1" class="users-form-toggle-input" {{ old('is_active', $priceList->is_active ?? true) ? 'checked' : '' }}>
                            <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                            <span class="users-form-toggle-label">القائمة نشطة</span>
                        </label>
                    </div>
                </div>

                <h6 class="users-form-section-title"><i class="fas fa-box"></i> أسعار المنتجات في هذه القائمة</h6>
                <div class="users-table-card users-table-card--nested">
                    <div class="table-responsive">
                        <table class="users-table" id="price-list-items-table">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th style="width: 160px;">سعر خاص</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody id="price-list-items-body">
                                @foreach ($oldItems as $index => $row)
                                    <tr class="price-list-item-row">
                                        <td>
                                            @include('admin.components.premium.product-select', [
                                                'name' => 'items[' . $index . '][product_id]',
                                                'id' => 'price_list_product_' . $index,
                                                'selected' => isset($row['product_id'], $oldProducts[$row['product_id']]) ? $oldProducts[$row['product_id']] : null,
                                                'required' => false,
                                            ])
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $index }}][price]" step="0.01" min="0" class="users-form-input" value="{{ $row['price'] ?? '' }}" placeholder="0.00">
                                        </td>
                                        <td>
                                            <button type="button" class="users-action-btn users-action-btn--delete price-list-remove-row" title="حذف"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <button type="button" class="users-btn-secondary" id="price-list-add-row" style="margin-top: 0.75rem;">
                    <i class="fas fa-plus"></i> إضافة منتج آخر
                </button>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ القائمة' }}</button>
                    <a href="{{ route('admin.price-lists.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
