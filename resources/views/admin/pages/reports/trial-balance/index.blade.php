@extends('admin.layouts.master')

@section('page-title')
    ميزان المراجعة
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto"><h5 class="page-title fs-21 mb-1">ميزان المراجعة</h5></div>
            <div>
                <a href="{{ route('admin.reports.trial-balance.index', array_merge(request()->only(['from', 'to']), ['format' => 'csv'])) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-csv me-1"></i> تصدير CSV</a>
            </div>
        </div>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3"><label class="form-label">من تاريخ</label><input type="date" name="from" class="form-control" value="{{ $from }}"></div>
                    <div class="col-md-3"><label class="form-label">إلى تاريخ</label><input type="date" name="to" class="form-control" value="{{ $to }}"></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> عرض</button></div>
                </form>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>كود الحساب</th>
                            <th>اسم الحساب</th>
                            <th>نوع الحساب</th>
                            <th class="text-end">إجمالي المدين</th>
                            <th class="text-end">إجمالي الدائن</th>
                            <th class="text-end">رصيد مدين</th>
                            <th class="text-end">رصيد دائن</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td>{{ $row->account_code }}</td>
                                <td>{{ $row->account_name }}</td>
                                <td>{{ $row->account_type }}</td>
                                <td class="text-end">{{ number_format($row->debit, 2) }}</td>
                                <td class="text-end">{{ number_format($row->credit, 2) }}</td>
                                <td class="text-end">{{ $row->balance_debit > 0 ? number_format($row->balance_debit, 2) : '—' }}</td>
                                <td class="text-end">{{ $row->balance_credit > 0 ? number_format($row->balance_credit, 2) : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">لا توجد حركات في الفترة المحددة.</td></tr>
                        @endforelse
                    </tbody>
                    @if(count($rows) > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">المجموع:</th>
                            <th class="text-end">{{ number_format($totalDebit, 2) }}</th>
                            <th class="text-end">{{ number_format($totalCredit, 2) }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@stop
