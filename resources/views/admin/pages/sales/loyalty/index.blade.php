@extends('admin.layouts.master')

@section('page-title')
    نقاط الولاء
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">نقاط الولاء</h5>
            </div>
            @can('loyalty-adjust')
            <div>
                <a href="{{ route('admin.loyalty.adjust-form') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> تعديل نقاط عميل
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
                            <div class="col-md-4">
                                <select name="customer_id" class="form-select">
                                    <option value="">جميع العملاء</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="type" class="form-select">
                                    <option value="">جميع الأنواع</option>
                                    <option value="earn" {{ request('type') === 'earn' ? 'selected' : '' }}>اكتساب</option>
                                    <option value="redeem" {{ request('type') === 'redeem' ? 'selected' : '' }}>استبدال</option>
                                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>تعديل</option>
                                    <option value="expire" {{ request('type') === 'expire' ? 'selected' : '' }}>انتهاء</option>
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
                                        <th>العميل</th>
                                        <th>النوع</th>
                                        <th>النقاط</th>
                                        <th>الرصيد بعد العملية</th>
                                        <th>الوصف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $tx)
                                        <tr>
                                            <td>{{ $tx->id }}</td>
                                            <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $tx->customer->name ?? '—' }}</td>
                                            <td>
                                                @switch($tx->type)
                                                    @case('earn') <span class="badge bg-success">اكتساب</span> @break
                                                    @case('redeem') <span class="badge bg-info">استبدال</span> @break
                                                    @case('adjustment') <span class="badge bg-warning text-dark">تعديل</span> @break
                                                    @case('expire') <span class="badge bg-secondary">انتهاء</span> @break
                                                    @default {{ $tx->type }}
                                                @endswitch
                                            </td>
                                            <td>{{ $tx->points > 0 ? '+' : '' }}{{ $tx->points }}</td>
                                            <td>{{ $tx->balance_after }}</td>
                                            <td class="text-start">{{ \Illuminate\Support\Str::limit($tx->description, 40) ?: '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">لا توجد حركات نقاط.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($transactions->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $transactions->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
