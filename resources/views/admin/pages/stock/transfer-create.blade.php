@extends('admin.layouts.master')

@section('page-title')
    تحويل مخزون جديد
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تحويل مخزون جديد</h5>
            </div>
            <div>
                <a href="{{ route('admin.stock.transfers.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form action="{{ route('admin.stock.transfers.store') }}" method="POST" id="transfer-form">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="from_warehouse_id" class="form-label">من مخزن <span class="text-danger">*</span></label>
                                    <select class="form-select" id="from_warehouse_id" name="from_warehouse_id" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach($warehouses as $w)
                                            <option value="{{ $w->id }}" {{ old('from_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="to_warehouse_id" class="form-label">إلى مخزن <span class="text-danger">*</span></label>
                                    <select class="form-select" id="to_warehouse_id" name="to_warehouse_id" required>
                                        <option value="">اختر المخزن</option>
                                        @foreach($warehouses as $w)
                                            <option value="{{ $w->id }}" {{ old('to_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="transfer_date" class="form-label">تاريخ التحويل <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="transfer_date" name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="notes" name="notes" rows="1">{{ old('notes') }}</textarea>
                            </div>

                            <h6 class="mb-2">البنود</h6>
                            <div class="table-responsive mb-2">
                                <table class="table table-bordered" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المنتج</th>
                                            <th>الكمية</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(old('items', []) as $i => $item)
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm" name="items[{{ $i }}][product_id]" required>
                                                        <option value="">اختر المنتج</option>
                                                        @foreach($products as $p)
                                                            <option value="{{ $p->id }}" {{ ($item['product_id'] ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.0001" min="0.0001" class="form-control form-control-sm" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? '' }}" required>
                                                </td>
                                                <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                                            </tr>
                                        @endforeach
                                        @if(count(old('items', [])) == 0)
                                            <tr class="item-row">
                                                <td>
                                                    <select class="form-select form-select-sm" name="items[0][product_id]" required>
                                                        <option value="">اختر المنتج</option>
                                                        @foreach($products as $p)
                                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.0001" min="0.0001" class="form-control form-control-sm" name="items[0][quantity]" required>
                                                </td>
                                                <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="add-item">
                                <i class="fas fa-plus me-1"></i> إضافة بند
                            </button>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-exchange-alt me-1"></i> تنفيذ التحويل</button>
                                <a href="{{ route('admin.stock.transfers.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->values());
    var rowIndex = {{ count(old('items', [])) }};
    if (rowIndex === 0) rowIndex = 1;

    document.getElementById('add-item')?.addEventListener('click', function() {
        var tbody = document.querySelector('#items-table tbody');
        var tr = document.createElement('tr');
        var options = '<option value="">اختر المنتج</option>' + products.map(function(p) {
            return '<option value="' + p.id + '">' + p.name + '</option>';
        }).join('');
        tr.innerHTML = '<td><select class="form-select form-select-sm" name="items[' + rowIndex + '][product_id]" required>' + options + '</select></td><td><input type="number" step="0.0001" min="0.0001" class="form-control form-control-sm" name="items[' + rowIndex + '][quantity]" required></td><td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>';
        tbody.appendChild(tr);
        rowIndex++;
    });
    document.getElementById('items-table')?.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            var row = e.target.closest('tr');
            if (document.querySelectorAll('#items-table tbody tr').length > 1) row.remove();
        }
    });
})();
</script>
@endpush
@stop
