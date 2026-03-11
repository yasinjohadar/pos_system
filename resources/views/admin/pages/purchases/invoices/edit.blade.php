@extends('admin.layouts.master')

@section('page-title')
    تعديل فاتورة الشراء {{ $purchaseInvoice->number }}
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تعديل فاتورة: {{ $purchaseInvoice->number }}</h5>
            </div>
            <div>
                <a href="{{ route('admin.purchase-invoices.show', $purchaseInvoice) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>يرجى تصحيح الأخطاء التالية:</strong>
                <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.purchase-invoices.update', $purchaseInvoice) }}" method="POST" id="invoice-form">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header"><h6 class="mb-0">بيانات الفاتورة</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">الفرع <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-select" required>
                                        @foreach($branches as $b)
                                            <option value="{{ $b->id }}" {{ old('branch_id', $purchaseInvoice->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">المورد</label>
                                    <select name="supplier_id" class="form-select">
                                        <option value="">بدون مورد</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}" {{ old('supplier_id', $purchaseInvoice->supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">مخزن الاستلام <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" id="warehouse_id" class="form-select" required>
                                        @foreach($warehouses as $w)
                                            <option value="{{ $w->id }}" {{ old('warehouse_id', $purchaseInvoice->warehouse_id) == $w->id ? 'selected' : '' }}>{{ $w->name }} ({{ $w->branch->name ?? '' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">تاريخ الفاتورة <span class="text-danger">*</span></label>
                                    <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', $purchaseInvoice->invoice_date->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">نسبة الضريبة %</label>
                                    <input type="number" step="0.01" name="tax_rate" class="form-control" value="{{ old('tax_rate', $purchaseInvoice->tax_rate) }}" min="0" max="100">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">نوع الخصم</label>
                                    <select name="discount_type" class="form-select">
                                        <option value="">بدون</option>
                                        <option value="fixed" {{ old('discount_type', $purchaseInvoice->discount_type) === 'fixed' ? 'selected' : '' }}>مبلغ</option>
                                        <option value="percent" {{ old('discount_type', $purchaseInvoice->discount_type) === 'percent' ? 'selected' : '' }}>نسبة %</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">قيمة الخصم</label>
                                    <input type="number" step="0.01" name="discount_value" class="form-control" value="{{ old('discount_value', $purchaseInvoice->discount_value) }}" min="0">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">ملاحظات</label>
                                    <textarea name="notes" class="form-control" rows="1">{{ old('notes', $purchaseInvoice->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">بنود الفاتورة</h6>
                            <button type="button" class="btn btn-sm btn-success" id="add-item-row"><i class="fas fa-plus me-1"></i> إضافة بند</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المنتج</th>
                                            <th width="100">الكمية</th>
                                            <th width="120">سعر الوحدة</th>
                                            <th width="120">الإجمالي</th>
                                            <th width="80"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-tbody">
                                        @php $items = old('items', $purchaseInvoice->items->map(fn($i) => ['product_id' => $i->product_id, 'quantity' => $i->quantity, 'unit_price' => $i->unit_price, 'warehouse_id' => $i->warehouse_id])->toArray()); @endphp
                                        @foreach($items as $i => $item)
                                        <tr class="item-row">
                                            <td>
                                                <select name="items[{{ $i }}][product_id]" class="form-select product-select">
                                                    <option value="">اختر المنتج</option>
                                                    @foreach($products as $p)
                                                        <option value="{{ $p->id }}" {{ ($item['product_id'] ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }} @if($p->unit) ({{ $p->unit->name }}) @endif</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="items[{{ $i }}][warehouse_id]" value="{{ $item['warehouse_id'] ?? '' }}">
                                            </td>
                                            <td><input type="number" step="0.001" name="items[{{ $i }}][quantity]" class="form-control item-qty" value="{{ $item['quantity'] ?? 1 }}" min="0.001"></td>
                                            <td><input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control item-price" value="{{ $item['unit_price'] ?? 0 }}" min="0"></td>
                                            <td><input type="text" class="form-control item-total" readonly value="0.00"></td>
                                            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> حفظ التعديلات</button>
                <a href="{{ route('admin.purchase-invoices.show', $purchaseInvoice) }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('items-tbody');

    function updateRowTotal(row) {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        row.querySelector('.item-total').value = (qty * price).toFixed(2);
    }

    function bindRow(row) {
        row.querySelector('.item-qty').addEventListener('input', () => updateRowTotal(row));
        row.querySelector('.item-price').addEventListener('input', () => updateRowTotal(row));
        row.querySelector('.remove-row').addEventListener('click', function() {
            if (tbody.querySelectorAll('.item-row').length > 1) row.remove();
        });
        updateRowTotal(row);
    }

    tbody.querySelectorAll('.item-row').forEach(bindRow);

    document.getElementById('add-item-row').addEventListener('click', function() {
        const idx = tbody.querySelectorAll('.item-row').length;
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td>
                <select name="items[${idx}][product_id]" class="form-select product-select">
                    <option value="">اختر المنتج</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} @if($p->unit) ({{ $p->unit->name }}) @endif</option>
                    @endforeach
                </select>
                <input type="hidden" name="items[${idx}][warehouse_id]" value="">
            </td>
            <td><input type="number" step="0.001" name="items[${idx}][quantity]" class="form-control item-qty" value="1" min="0.001"></td>
            <td><input type="number" step="0.01" name="items[${idx}][unit_price]" class="form-control item-price" value="0" min="0"></td>
            <td><input type="text" class="form-control item-total" readonly value="0.00"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        bindRow(tr);
    });
});
</script>
@endpush
@stop
