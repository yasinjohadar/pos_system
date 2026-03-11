@extends('admin.layouts.master')

@section('page-title')
    مرتجعات البيع
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">مرتجعات البيع</h5>
            </div>
            @can('sale-return-create')
            <div>
                <a href="{{ route('admin.sale-returns.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> مرتجع جديد
                </a>
            </div>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="GET" class="mb-4 row g-3">
                            <div class="col-md-3">
                                <input type="text" name="return_number" class="form-control" placeholder="رقم المرتجع" value="{{ request('return_number') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="sale_invoice_id" class="form-control" placeholder="رقم الفاتورة (ID)" value="{{ request('sale_invoice_id') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">الحالة</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغى</option>
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
                                        <th>رقم المرتجع</th>
                                        <th>الفاتورة الأصلية</th>
                                        <th>التاريخ</th>
                                        <th>المخزن</th>
                                        <th>المبلغ المرتجع</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($returns as $ret)
                                        <tr>
                                            <td>{{ $ret->return_number }}</td>
                                            <td>
                                                <a href="{{ route('admin.sale-invoices.show', $ret->sale_invoice_id) }}">{{ $ret->saleInvoice->number ?? $ret->sale_invoice_id }}</a>
                                            </td>
                                            <td>{{ $ret->return_date->format('Y-m-d') }}</td>
                                            <td>{{ $ret->warehouse->name ?? '—' }}</td>
                                            <td>{{ number_format($ret->total_refund, 2) }}</td>
                                            <td>
                                                @if($ret->status === 'pending')
                                                    <span class="badge bg-warning">قيد الانتظار</span>
                                                @elseif($ret->status === 'completed')
                                                    <span class="badge bg-success">مكتمل</span>
                                                @else
                                                    <span class="badge bg-danger">ملغى</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('sale-return-show')
                                                <a href="{{ route('admin.sale-returns.show', $ret) }}" class="btn btn-sm btn-info" title="عرض"><i class="fas fa-eye"></i></a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">لا توجد مرتجعات.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($returns->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $returns->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
