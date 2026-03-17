@extends('admin.layouts.master')

@section('page-title')
    القيود اليومية
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">القيود اليومية</h4>
        </div>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2"><label class="form-label">من تاريخ</label><input type="date" name="from" class="form-control" value="{{ request('from') }}"></div>
                    <div class="col-md-2"><label class="form-label">إلى تاريخ</label><input type="date" name="to" class="form-control" value="{{ request('to') }}"></div>
                    <div class="col-md-2"><label class="form-label">المرجع</label>
                        <select name="reference_type" class="form-select">
                            <option value="">الكل</option>
                            <option value="App\Models\SaleInvoice" {{ request('reference_type') == 'App\Models\SaleInvoice' ? 'selected' : '' }}>فاتورة بيع</option>
                            <option value="App\Models\PurchaseInvoice" {{ request('reference_type') == 'App\Models\PurchaseInvoice' ? 'selected' : '' }}>فاتورة شراء</option>
                            <option value="App\Models\CashVoucher" {{ request('reference_type') == 'App\Models\CashVoucher' ? 'selected' : '' }}>سند</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary">بحث</button></div>
                </form>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>رقم القيد</th>
                            <th>التاريخ</th>
                            <th>الوصف</th>
                            <th>المرجع</th>
                            <th>أنشئ بواسطة</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $e)
                            <tr>
                                <td>{{ $e->entry_number }}</td>
                                <td>{{ $e->entry_date->format('Y-m-d') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($e->description, 40) }}</td>
                                <td>{{ $e->reference_type ? class_basename($e->reference_type) . ' #' . $e->reference_id : '—' }}</td>
                                <td>{{ $e->createdBy->name ?? '—' }}</td>
                                <td>
                                    @can('journal-entry-show')
                                        <a href="{{ route('admin.journal-entries.show', $e) }}" class="btn btn-sm btn-info">عرض</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">لا توجد قيود.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-3">{{ $entries->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
</div>
@stop
