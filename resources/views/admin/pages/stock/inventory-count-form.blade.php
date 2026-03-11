@extends('admin.layouts.master')

@section('page-title')
    نموذج جرد المخزون
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">جرد المخزون — {{ $warehouse->name }}</h5>
            </div>
            <div>
                <a href="{{ route('admin.stock.inventory-count.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
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
                        <form action="{{ route('admin.stock.inventory-count.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">
                            <div class="mb-3">
                                <label for="count_date" class="form-label">تاريخ الجرد <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="count_date" name="count_date" value="{{ old('count_date', date('Y-m-d')) }}" required style="max-width:200px">
                            </div>

                            <p class="text-muted">أدخل الرصيد الفعلي المُعدّ بعد الجرد. سيتم إنشاء حركات تسوية للفرق بين الرصيد المحسب والفعلي.</p>

                            <div class="table-responsive mb-4">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>المنتج</th>
                                            <th>الرصيد الحالي (المحسب)</th>
                                            <th>حد التنبيه</th>
                                            <th>الرصيد الفعلي بعد الجرد <span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($balances as $b)
                                            <tr>
                                                <td>{{ $b->product->name ?? '—' }}</td>
                                                <td>{{ number_format($b->quantity, 2) }}</td>
                                                <td>{{ $b->product ? $b->product->min_stock_alert : '—' }}</td>
                                                <td>
                                                    <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $b->product_id }}">
                                                    <input type="number" step="0.0001" min="0" class="form-control form-control-sm" name="items[{{ $loop->index }}][actual_quantity]" value="{{ old('items.'.$loop->index.'.actual_quantity', $b->quantity) }}" required style="max-width:120px">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">لا توجد أرصدة في هذا المخزن. قم بإدخال كميات أولاً عبر حركات الإدخال.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($balances->count() > 0)
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i> تنفيذ الجرد</button>
                                    <a href="{{ route('admin.stock.inventory-count.index') }}" class="btn btn-secondary">إلغاء</a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
