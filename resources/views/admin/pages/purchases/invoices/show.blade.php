@extends('admin.layouts.master')

@section('page-title')
    فاتورة الشراء {{ $purchaseInvoice->number }}
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">فاتورة الشراء: {{ $purchaseInvoice->number }}</h5>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if($purchaseInvoice->status === \App\Models\PurchaseInvoice::STATUS_DRAFT)
                    @can('purchase-invoice-confirm')
                    <form action="{{ route('admin.purchase-invoices.confirm', $purchaseInvoice) }}" method="POST" class="d-inline" onsubmit="return confirm('اعتماد الفاتورة سينشئ حركات إدخال مخزون. متابعة؟');">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i> اعتماد الفاتورة</button>
                    </form>
                    @endcan
                    @can('purchase-invoice-edit')
                    <a href="{{ route('admin.purchase-invoices.edit', $purchaseInvoice) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i> تعديل</a>
                    @endcan
                    @can('purchase-invoice-delete')
                    <form action="{{ route('admin.purchase-invoices.destroy', $purchaseInvoice) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف الفاتورة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash me-1"></i> حذف</button>
                    </form>
                    @endcan
                @endif
                <a href="{{ route('admin.purchase-invoices.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-right me-1"></i> رجوع</a>
                @if($purchaseInvoice->status === \App\Models\PurchaseInvoice::STATUS_CONFIRMED)
                <a href="{{ route('admin.purchase-returns.create', ['purchase_invoice_id' => $purchaseInvoice->id]) }}" class="btn btn-outline-warning btn-sm"><i class="fas fa-undo me-1"></i> مرتجع</a>
                @endif
            </div>
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
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header"><h6 class="mb-0">بيانات الفاتورة</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless mb-0">
                                    <tr><td class="text-muted">الفرع:</td><td>{{ $purchaseInvoice->branch->name ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">المورد:</td><td>{{ $purchaseInvoice->supplier->name ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">مخزن الاستلام:</td><td>{{ $purchaseInvoice->warehouse->name ?? '—' }}</td></tr>
                                    <tr><td class="text-muted">التاريخ:</td><td>{{ $purchaseInvoice->invoice_date->format('Y-m-d') }}</td></tr>
                                    <tr><td class="text-muted">الحالة:</td>
                                        <td>
                                            @if($purchaseInvoice->status === 'draft')
                                                <span class="badge bg-secondary">مسودة</span>
                                            @elseif($purchaseInvoice->status === 'confirmed')
                                                <span class="badge bg-success">معتمدة</span>
                                            @else
                                                <span class="badge bg-danger">ملغاة</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless mb-0">
                                    <tr><td class="text-muted">حالة الدفع:</td>
                                        <td>
                                            @if($purchaseInvoice->payment_status === 'paid')
                                                <span class="badge bg-success">مدفوع</span>
                                            @elseif($purchaseInvoice->payment_status === 'partial')
                                                <span class="badge bg-warning">جزئي</span>
                                            @else
                                                <span class="badge bg-secondary">معلق</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr><td class="text-muted">المستخدم:</td><td>{{ $purchaseInvoice->user->name ?? '—' }}</td></tr>
                                    @if($purchaseInvoice->notes)
                                    <tr><td class="text-muted">ملاحظات:</td><td>{{ $purchaseInvoice->notes }}</td></tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header"><h6 class="mb-0">بنود الفاتورة</h6></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>المنتج</th>
                                        <th>الكمية</th>
                                        <th>سعر الوحدة</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseInvoice->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? '—' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if($purchaseInvoice->status === \App\Models\PurchaseInvoice::STATUS_CONFIRMED && $purchaseInvoice->supplier_id && $paymentMethods->isNotEmpty())
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header"><h6 class="mb-0">تسجيل دفعة للمورد</h6></div>
                    <div class="card-body">
                        <form action="{{ route('admin.purchase-invoices.payments.store', $purchaseInvoice) }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control" value="{{ number_format($purchaseInvoice->remaining_amount, 2, '.', '') }}" max="{{ $purchaseInvoice->remaining_amount }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                                <select name="payment_method_id" class="form-select" required>
                                    @foreach($paymentMethods as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(isset($treasuries) && $treasuries->isNotEmpty())
                            <div class="col-md-3">
                                <label class="form-label">خزنة / بنك</label>
                                <select name="treasury_id" class="form-select">
                                    <option value="">— اختياري —</option>
                                    @foreach($treasuries as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->type === 'cashbox' ? 'خزنة' : 'بنك' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-3">
                                <label class="form-label">تاريخ الدفع <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">مرجع (اختياري)</label>
                                <input type="text" name="reference" class="form-control" placeholder="رقم شيك / تحويل">
                            </div>
                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <input type="text" name="notes" class="form-control">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> تسجيل الدفعة</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header"><h6 class="mb-0">الإجماليات</h6></div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr><td>المجموع الفرعي:</td><td class="text-end">{{ number_format($purchaseInvoice->subtotal, 2) }}</td></tr>
                            @if($purchaseInvoice->discount_amount > 0)
                            <tr><td>الخصم:</td><td class="text-end">- {{ number_format($purchaseInvoice->discount_amount, 2) }}</td></tr>
                            @endif
                            @if($purchaseInvoice->tax_amount > 0)
                            <tr><td>الضريبة ({{ $purchaseInvoice->tax_rate }}%):</td><td class="text-end">{{ number_format($purchaseInvoice->tax_amount, 2) }}</td></tr>
                            @endif
                            <tr><th>الإجمالي:</th><td class="text-end"><strong>{{ number_format($purchaseInvoice->total, 2) }}</strong></td></tr>
                            @if($purchaseInvoice->status === \App\Models\PurchaseInvoice::STATUS_CONFIRMED)
                            <tr><td>المدفوع:</td><td class="text-end text-success">{{ number_format($purchaseInvoice->total_paid, 2) }}</td></tr>
                            <tr><td>المتبقي:</td><td class="text-end"><strong>{{ number_format($purchaseInvoice->remaining_amount, 2) }}</strong></td></tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($purchaseInvoice->supplierPayments->isNotEmpty())
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header"><h6 class="mb-0">الدفعات</h6></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الطريقة</th>
                                        <th>خزنة/بنك</th>
                                        <th>المبلغ</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseInvoice->supplierPayments as $pay)
                                        <tr>
                                            <td>{{ $pay->payment_date->format('Y-m-d') }}</td>
                                            <td>{{ $pay->paymentMethod->name ?? '—' }}</td>
                                            <td>{{ $pay->treasury->name ?? '—' }}</td>
                                            <td>{{ number_format($pay->amount, 2) }}</td>
                                            <td>
                                                <form action="{{ route('admin.purchase-invoices.payments.destroy', [$purchaseInvoice, $pay]) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف هذه الدفعة؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
