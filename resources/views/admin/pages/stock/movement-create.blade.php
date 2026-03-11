@extends('admin.layouts.master')

@section('page-title')
    حركة مخزون جديدة
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">حركة مخزون جديدة</h5>
            </div>
            <div>
                <a href="{{ route('admin.stock.movements.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
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
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form action="{{ route('admin.stock.movements.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="type" class="form-label">نوع الحركة <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>إدخال</option>
                                    <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>صرف</option>
                                    <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>تسوية</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="product_id" class="form-label">المنتج <span class="text-danger">*</span></label>
                                <select class="form-select" id="product_id" name="product_id" required>
                                    <option value="">اختر المنتج</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->barcode ?? '—' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="warehouse_id" class="form-label">المخزن <span class="text-danger">*</span></label>
                                <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                                    <option value="">اختر المخزن</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">الكمية <span class="text-danger">*</span></label>
                                <input type="number" step="0.0001" min="0" class="form-control" id="quantity" name="quantity" value="{{ old('quantity') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="movement_date" class="form-label">تاريخ الحركة <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="movement_date" name="movement_date" value="{{ old('movement_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> حفظ</button>
                                <a href="{{ route('admin.stock.movements.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
