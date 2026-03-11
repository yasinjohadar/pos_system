@extends('admin.layouts.master')

@section('page-title')
    حركات المخزون
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">حركات المخزون</h5>
            </div>
            @can('stock-movement-create')
            <div>
                <a href="{{ route('admin.stock.movements.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> حركة جديدة
                </a>
                <a href="{{ route('admin.stock.balances.index') }}" class="btn btn-outline-primary btn-sm">أرصدة المخزون</a>
            </div>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="GET" class="mb-4 row g-3">
                            <div class="col-md-2">
                                <select name="warehouse_id" class="form-select">
                                    <option value="">جميع المخازن</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="type" class="form-select">
                                    <option value="">جميع الأنواع</option>
                                    @foreach(\App\Models\StockMovement::TYPE_LABELS as $k => $v)
                                        <option value="{{ $k }}" {{ request('type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="من تاريخ">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="إلى تاريخ">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-search me-1"></i> بحث</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>التاريخ</th>
                                        <th>المنتج</th>
                                        <th>المخزن</th>
                                        <th>النوع</th>
                                        <th>الكمية</th>
                                        <th>المستخدم</th>
                                        <th>ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($movements as $m)
                                        <tr>
                                            <td>{{ $m->id }}</td>
                                            <td>{{ $m->movement_date->format('Y-m-d') }}</td>
                                            <td>{{ $m->product->name ?? '—' }}</td>
                                            <td>{{ $m->warehouse->name ?? '—' }}</td>
                                            <td>{{ \App\Models\StockMovement::TYPE_LABELS[$m->type] ?? $m->type }}</td>
                                            <td class="{{ $m->quantity >= 0 ? 'text-success' : 'text-danger' }}">{{ $m->quantity >= 0 ? '+' : '' }}{{ number_format($m->quantity, 2) }}</td>
                                            <td>{{ $m->user->name ?? '—' }}</td>
                                            <td>{{ Str::limit($m->notes, 30) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">لا توجد حركات.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($movements->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $movements->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
