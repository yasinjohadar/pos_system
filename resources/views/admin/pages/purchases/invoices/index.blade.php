@extends('admin.layouts.master')

@section('page-title')
    فواتير الشراء
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">فواتير الشراء</h5>
            </div>
            @can('purchase-invoice-create')
            <div>
                <a href="{{ route('admin.purchase-invoices.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> فاتورة شراء جديدة
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
                            <div class="col-md-2">
                                <input type="text" name="number" class="form-control" placeholder="رقم الفاتورة" value="{{ request('number') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="branch_id" class="form-select">
                                    <option value="">الفرع</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="supplier_id" class="form-select">
                                    <option value="">المورد</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">الحالة</option>
                                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>معتمدة</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="payment_status" class="form-select">
                                    <option value="">حالة الدفع</option>
                                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>معلق</option>
                                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>جزئي</option>
                                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>مدفوع</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-search"></i></button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>الرقم</th>
                                        <th>التاريخ</th>
                                        <th>الفرع</th>
                                        <th>المورد</th>
                                        <th>الإجمالي</th>
                                        <th>حالة الدفع</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $inv)
                                        <tr>
                                            <td>{{ $inv->number }}</td>
                                            <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
                                            <td>{{ $inv->branch->name ?? '—' }}</td>
                                            <td>{{ $inv->supplier->name ?? '—' }}</td>
                                            <td>{{ number_format($inv->total, 2) }}</td>
                                            <td>
                                                @if($inv->payment_status === 'paid')
                                                    <span class="badge bg-success">مدفوع</span>
                                                @elseif($inv->payment_status === 'partial')
                                                    <span class="badge bg-warning">جزئي</span>
                                                @else
                                                    <span class="badge bg-secondary">معلق</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($inv->status === 'draft')
                                                    <span class="badge bg-secondary">مسودة</span>
                                                @elseif($inv->status === 'confirmed')
                                                    <span class="badge bg-success">معتمدة</span>
                                                @else
                                                    <span class="badge bg-danger">ملغاة</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                                    @can('purchase-invoice-show')
                                                    <a href="{{ route('admin.purchase-invoices.show', $inv) }}" class="btn btn-sm btn-info" title="عرض"><i class="fas fa-eye"></i></a>
                                                    @endcan
                                                    @can('purchase-invoice-edit')
                                                    @if($inv->status === 'draft')
                                                    <a href="{{ route('admin.purchase-invoices.edit', $inv) }}" class="btn btn-sm btn-primary" title="تعديل"><i class="fas fa-edit"></i></a>
                                                    @endif
                                                    @endcan
                                                    @can('purchase-invoice-delete')
                                                    @if($inv->status === 'draft')
                                                    <form action="{{ route('admin.purchase-invoices.destroy', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف الفاتورة؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                    @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">لا توجد فواتير شراء.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($invoices->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $invoices->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
