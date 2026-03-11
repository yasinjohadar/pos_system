@extends('admin.layouts.master')

@section('page-title')
    السنوات المالية
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">السنوات المالية</h5>
            </div>
            <div>
                <a href="{{ route('admin.fiscal-years.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> سنة مالية جديدة
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>الاسم</th>
                                <th>من</th>
                                <th>إلى</th>
                                <th>نشطة</th>
                                <th>مقفلة</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($years as $y)
                                <tr>
                                    <td>{{ $y->name }}</td>
                                    <td>{{ $y->start_date->format('Y-m-d') }}</td>
                                    <td>{{ $y->end_date->format('Y-m-d') }}</td>
                                    <td>{!! $y->is_active ? '<span class="badge bg-success">نعم</span>' : '<span class="badge bg-secondary">لا</span>' !!}</td>
                                    <td>{!! $y->is_closed ? '<span class="badge bg-danger">نعم</span>' : '<span class="badge bg-success">لا</span>' !!}</td>
                                    <td>
                                        @if(!$y->is_closed)
                                        <form action="{{ route('admin.fiscal-years.close', $y) }}" method="POST" onsubmit="return confirm('إقفال هذه السنة المالية؟');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">إقفال</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد سنوات مالية.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

