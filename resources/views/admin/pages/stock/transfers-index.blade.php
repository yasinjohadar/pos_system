@extends('admin.layouts.master')

@section('page-title')
    تحويلات المخزون
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تحويلات المخزون</h5>
            </div>
            @can('stock-transfer-create')
            <div>
                <a href="{{ route('admin.stock.transfers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-exchange-alt me-1"></i> تحويل جديد
                </a>
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
                                <select name="from_warehouse_id" class="form-select">
                                    <option value="">من مخزن</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ request('from_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="to_warehouse_id" class="form-select">
                                    <option value="">إلى مخزن</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ request('to_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                </select>
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
                                        <th>من مخزن</th>
                                        <th>إلى مخزن</th>
                                        <th>الحالة</th>
                                        <th>المستخدم</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transfers as $t)
                                        <tr>
                                            <td>{{ $t->id }}</td>
                                            <td>{{ $t->transfer_date->format('Y-m-d') }}</td>
                                            <td>{{ $t->fromWarehouse->name ?? '—' }}</td>
                                            <td>{{ $t->toWarehouse->name ?? '—' }}</td>
                                            <td>
                                                @if($t->status == 'completed')
                                                    <span class="badge bg-success">مكتمل</span>
                                                @elseif($t->status == 'pending')
                                                    <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $t->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $t->user->name ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('admin.stock.transfers.show', $t) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">لا توجد تحويلات.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($transfers->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $transfers->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
