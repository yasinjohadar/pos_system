@extends('admin.layouts.master')

@section('page-title')
إضافة عرض جديد
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">إضافة عرض جديد</h4>
            <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">رجوع لقائمة العروض</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.promotions.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">اسم العرض</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">نوع الخصم</label>
                            <select name="type" class="form-select" required>
                                <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>نسبة مئوية</option>
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">قيمة الخصم</label>
                            <input type="number" step="0.01" min="0" name="value" value="{{ old('value', 0) }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">تاريخ البداية</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">تاريخ النهاية</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الحد الأدنى لقيمة الفاتورة</label>
                            <input type="number" step="0.01" min="0" name="min_invoice_amount" value="{{ old('min_invoice_amount') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الحد الأدنى للكمية</label>
                            <input type="number" step="0.01" min="0" name="min_qty" value="{{ old('min_qty') }}" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>

                    <hr>

                    <h5 class="mb-3">المنتجات المشمولة بالعرض</h5>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle">
                            <thead>
                            <tr>
                                <th style="width: 60%">المنتج</th>
                                <th style="width: 30%">أقصى كمية للعرض (اختياري)</th>
                                <th style="width: 10%"></th>
                            </tr>
                            </thead>
                            <tbody id="promotion-items-body">
                            @php
                                $oldItems = old('items', [[]]);
                            @endphp
                            @foreach($oldItems as $index => $row)
                                <tr>
                                    <td>
                                        <select name="items[{{ $index }}][product_id]" class="form-select">
                                            <option value="">-- اختر منتجاً --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ (isset($row['product_id']) && (int)$row['product_id'] === $product->id) ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][max_qty]" step="0.01" min="0"
                                               value="{{ $row['max_qty'] ?? '' }}" class="form-control">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removePromotionRow(this)">حذف</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-outline-primary mb-3" onclick="addPromotionRow()">إضافة منتج آخر</button>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">حفظ العرض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let promotionRowIndex = {{ count(old('items', [[]])) }};

    function addPromotionRow() {
        const tbody = document.getElementById('promotion-items-body');
        const index = promotionRowIndex++;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="items[${index}][product_id]" class="form-select">
                    <option value="">-- اختر منتجاً --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${index}][max_qty]" step="0.01" min="0" class="form-control">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="removePromotionRow(this)">حذف</button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function removePromotionRow(button) {
        const row = button.closest('tr');
        if (row) {
            row.remove();
        }
    }
</script>
@endpush
@stop

