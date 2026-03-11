@extends('admin.layouts.master')

@section('page-title')
    التحويلات المالية
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">التحويلات المالية</h5>
            </div>
            @can('financial-transfer-create')
            <div>
                <a href="{{ route('admin.financial-transfers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> تحويل جديد
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

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="GET" class="mb-4 row g-3">
                            <div class="col-md-3">
                                <label class="form-label">من تاريخ</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">إلى تاريخ</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-search me-1"></i> بحث</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>التاريخ</th>
                                        <th>من</th>
                                        <th>إلى</th>
                                        <th>المبلغ</th>
                                        <th>المرجع</th>
                                        <th>المستخدم</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transfers as $t)
                                        <tr>
                                            <td>{{ $t->id }}</td>
                                            <td>{{ $t->transfer_date->format('Y-m-d') }}</td>
                                            <td>{{ $t->from_source_name }}</td>
                                            <td>{{ $t->to_target_name }}</td>
                                            <td>{{ number_format($t->amount, 2) }}</td>
                                            <td>{{ $t->reference ?? '—' }}</td>
                                            <td>{{ $t->user->name ?? '—' }}</td>
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
