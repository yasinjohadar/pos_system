@extends('admin.layouts.master')

@section('page-title')
    منتجات بدون مبيعات
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4">
            <h5 class="page-title fs-21 mb-1">منتجات بدون مبيعات</h5>
            <a href="{{ route('admin.reports.product-performance.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light"><tr><th>المنتج</th><th>التصنيف</th><th>الباركود</th></tr></thead>
                    <tbody>
                        @forelse($rows as $p)
                            <tr><td>{{ $p->name }}</td><td>{{ $p->category->name ?? '—' }}</td><td>{{ $p->barcode ?? '—' }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center">لا توجد منتجات بدون مبيعات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
