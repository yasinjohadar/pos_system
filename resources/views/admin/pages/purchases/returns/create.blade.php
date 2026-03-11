@extends('admin.layouts.master')

@section('page-title')
    {{ $purchaseInvoice ? 'مرتجع من فاتورة ' . $purchaseInvoice->number : 'مرتجع شراء جديد' }}
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">
                    @if($purchaseInvoice)
                        مرتجع من فاتورة: {{ $purchaseInvoice->number }}
                    @else
                        مرتجع شراء جديد
                    @endif
                </h5>
            </div>
            <div>
                <a href="{{ $purchaseInvoice ? route('admin.purchase-invoices.show', $purchaseInvoice) : route('admin.purchase-returns.index') }}" class="btn btn-secondary btn-sm">
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

        <form action="{{ route('admin.purchase-returns.store') }}" method="POST">
            @csrf
            @if($purchaseInvoice)
            <input type="hidden" name="purchase_invoice_id" value="{{ $purchaseInvoice->id }}">
            @endif
            @if(!$purchaseInvoice)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <label class="form-label">الفاتورة الأصلية <span class="text-danger">*</span></label>
                    <select name="purchase_invoice_id" id="purchase_invoice_id" class="form-select @error('purchase_invoice_id') is-invalid @enderror" required>
                        <option value="">اختر الفاتورة</option>
                        @foreach($invoices ?? [] as $inv)
                            <option value="{{ $inv->id }}" {{ old('purchase_invoice_id') == $inv->id ? 'selected' : '' }}>{{ $inv->number }} - {{ $inv->branch->name ?? '' }} - {{ $inv->invoice_date->format('Y-m-d') }}</option>
                        @endforeach
                    </select>
                    @error('purchase_invoice_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <p class="text-muted small mt-1">أو أنشئ المرتجع من صفحة فاتورة الشراء (زر "مرتجع").</p>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header"><h6 class="mb-0">بيانات المرتجع</h6></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">مخزن الصرف <span class="text-danger">*</span></label>
                                    <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach($warehouses ?? [] as $w)
                                            <option value="{{ $w->id }}" {{ old('warehouse_id', $purchaseInvoice->warehouse_id ?? '') == $w->id ? 'selected' : '' }}>{{ $w->name }} ({{ $w->branch->name ?? '' }})</option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">تاريخ المرتجع <span class="text-danger">*</span></label>
                                    <input type="date" name="return_date" class="form-control @error('return_date') is-invalid @enderror" value="{{ old('return_date', date('Y-m-d')) }}" required>
                                    @error('return_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">ملاحظات</label>
                                    <textarea name="notes" class="form-control" rows="1">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header"><h6 class="mb-0">بنود المرتجع</h6></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="return-items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المنتج</th>
                                            <th width="100">الكمية</th>
                                            <th width="120">سعر الوحدة</th>
                                            <th width="120">الإجمالي</th>
                                            <th width="80"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="return-items-tbody">
                                        @if($purchaseInvoice)
                                            @foreach($purchaseInvoice->items as $i => $item)
                                            <tr class="return-item-row">
                                                <td>
                                                    <input type="hidden" name="items[{{ $i }}][purchase_invoice_item_id]" value="{{ $item->id }}">
                                                    <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $item->product_id }}">
                                                    {{ $item->product->name ?? '—' }}
                                                    <small class="text-muted">(القابل للمرتجع: {{ $item->quantity_remaining }})</small>
                                                </td>
                                                <td><input type="number" step="0.001" name="items[{{ $i }}][quantity]" class="form-control return-qty" value="0" max="{{ $item->quantity_remaining }}" min="0"></td>
                                                <td><input type="number" step="0.01" name="items[{{ $i }}][unit_price]" class="form-control return-price" value="{{ $item->unit_price }}" min="0"></td>
                                                <td><input type="text" class="form-control return-line-total" readonly value="0.00"></td>
                                                <td></td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr class="return-item-row">
                                                <td colspan="5" class="text-center text-muted">اختر الفاتورة أولاً ثم أعد تحميل الصفحة أو انتقل لصفحة الفاتورة واضغط "مرتجع".</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($purchaseInvoice && $purchaseInvoice->items->isNotEmpty())
            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> حفظ المرتجع</button>
                <a href="{{ route('admin.purchase-invoices.show', $purchaseInvoice) }}" class="btn btn-secondary">إلغاء</a>
            </div>
            @endif
        </form>
    </div>
</div>

@if(!$purchaseInvoice && isset($invoices))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('purchase_invoice_id');
    if (sel) {
        sel.addEventListener('change', function() {
            var id = this.value;
            if (id) {
                window.location = '{{ route("admin.purchase-returns.create") }}?purchase_invoice_id=' + id;
            }
        });
    }
});
</script>
@endpush
@endif

@if($purchaseInvoice)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.return-item-row').forEach(function(row) {
        const qtyInput = row.querySelector('.return-qty');
        const priceInput = row.querySelector('.return-price');
        const totalInput = row.querySelector('.return-line-total');
        function update() {
            const q = parseFloat(qtyInput.value) || 0;
            const p = parseFloat(priceInput.value) || 0;
            totalInput.value = (q * p).toFixed(2);
        }
        qtyInput.addEventListener('input', update);
        priceInput.addEventListener('input', update);
        update();
    });
});
</script>
@endpush
@endif
@stop
