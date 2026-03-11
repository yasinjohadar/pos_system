@extends('admin.layouts.master')

@section('page-title')
    جرد المخزون
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">جرد المخزون</h5>
            </div>
            <div>
                <a href="{{ route('admin.stock.balances.index') }}" class="btn btn-outline-primary btn-sm">أرصدة المخزون</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.stock.inventory-count.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label for="warehouse_id" class="form-label">اختر المخزن للجرد</label>
                                <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                                    <option value="">— اختر المخزن —</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-list me-1"></i> عرض نموذج الجرد</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
