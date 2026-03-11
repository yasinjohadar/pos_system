@extends('admin.layouts.master')

@section('page-title')
    الشيكات
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">الشيكات</h5>
            </div>
            @can('check-create')
            <div>
                <a href="{{ route('admin.checks.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> إضافة شيك
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
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">الحالة</option>
                                    <option value="under_collection" {{ request('status') === 'under_collection' ? 'selected' : '' }}>تحت التحصيل</option>
                                    <option value="collected" {{ request('status') === 'collected' ? 'selected' : '' }}>محصل</option>
                                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>مرتجع</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="من تاريخ">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="إلى تاريخ">
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
                                        <th>رقم الشيك</th>
                                        <th>البنك</th>
                                        <th>المبلغ</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($checks as $c)
                                        <tr>
                                            <td>{{ $c->id }}</td>
                                            <td>{{ $c->check_number }}</td>
                                            <td>{{ $c->bank_account_id ? ($c->bankAccount->name ?? '—') : ($c->bank_name ?? '—') }}</td>
                                            <td>{{ number_format($c->amount, 2) }}</td>
                                            <td>{{ $c->due_date->format('Y-m-d') }}</td>
                                            <td>
                                                @if($c->status === \App\Models\Check::STATUS_UNDER_COLLECTION)
                                                    <span class="badge bg-warning">تحت التحصيل</span>
                                                @elseif($c->status === \App\Models\Check::STATUS_COLLECTED)
                                                    <span class="badge bg-success">محصل</span>
                                                @else
                                                    <span class="badge bg-danger">مرتجع</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('check-show')
                                                <a href="{{ route('admin.checks.show', $c) }}" class="btn btn-sm btn-info" title="عرض"><i class="fas fa-eye"></i></a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">لا توجد شيكات.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($checks->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $checks->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
