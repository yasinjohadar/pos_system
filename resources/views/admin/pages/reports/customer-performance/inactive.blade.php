@extends('admin.layouts.master')

@section('page-title')
    عملاء غير نشطين
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4">
            <h5 class="page-title fs-21 mb-1">عملاء غير نشطين</h5>
            <a href="{{ route('admin.reports.customer-performance.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">لم يشتروا منذ (يوم)</label>
                        <input type="number" name="days" class="form-control" value="{{ $days }}" min="1">
                    </div>
                    <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary">عرض</button></div>
                </form>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light"><tr><th>العميل</th><th>الهاتف</th><th>البريد</th></tr></thead>
                    <tbody>
                        @forelse($rows as $c)
                            <tr><td>{{ $c->name }}</td><td>{{ $c->phone ?? '—' }}</td><td>{{ $c->email ?? '—' }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center">لا يوجد عملاء غير نشطين.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
