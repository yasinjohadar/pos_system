@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المورد
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تفاصيل المورد: {{ $supplier->name }}</h5>
            </div>
            <div class="d-flex gap-2">
                @can('supplier-edit')
                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> تعديل
                </a>
                @endcan
                @can('supplier-show')
                <a href="{{ route('admin.suppliers.statement', $supplier) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-file-invoice me-1"></i> كشف حساب
                </a>
                @endcan
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">بيانات المورد</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="140">الاسم:</td>
                                <td>{{ $supplier->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">الهاتف:</td>
                                <td>{{ $supplier->phone ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">البريد:</td>
                                <td>{{ $supplier->email ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">العنوان:</td>
                                <td>{{ $supplier->address ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">رصيد افتتاحي:</td>
                                <td>{{ number_format($supplier->opening_balance, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">إجمالي المشتريات:</td>
                                <td>{{ number_format($supplier->total_purchases, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">إجمالي المرتجعات:</td>
                                <td>{{ number_format($supplier->total_returns, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">إجمالي المدفوعات:</td>
                                <td>{{ number_format($supplier->total_paid, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">الرصيد المستحق للمورد:</td>
                                <td><strong>{{ number_format($supplier->balance, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">الحالة:</td>
                                <td>
                                    @if($supplier->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>
                            </tr>
                            @if($supplier->notes)
                            <tr>
                                <td class="text-muted">ملاحظات:</td>
                                <td>{{ $supplier->notes }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">آخر فواتير الشراء</h6>
                        <a href="{{ route('admin.purchase-invoices.index', ['supplier_id' => $supplier->id]) }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th>التاريخ</th>
                                        <th>الإجمالي</th>
                                        <th>حالة الدفع</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($supplier->purchaseInvoices as $inv)
                                        <tr>
                                            <td>{{ $inv->number }}</td>
                                            <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
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
                                                <a href="{{ route('admin.purchase-invoices.show', $inv) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">لا توجد فواتير شراء لهذا المورد.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
