@extends('admin.layouts.master')

@section('page-title')
    الخزائن والبنوك
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">الخزائن والبنوك</h5>
            </div>
            @can('treasury-create')
            <div>
                <a href="{{ route('admin.treasuries.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> إضافة خزنة / بنك
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
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="GET" class="mb-4 row g-3">
                            <div class="col-md-3">
                                <select name="type" class="form-select">
                                    <option value="">النوع</option>
                                    <option value="cashbox" {{ request('type') === 'cashbox' ? 'selected' : '' }}>خزنة</option>
                                    <option value="bank" {{ request('type') === 'bank' ? 'selected' : '' }}>بنك</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="is_active" class="form-select">
                                    <option value="">جميع الحالات</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
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
                                        <th>الاسم</th>
                                        <th>النوع</th>
                                        <th>الفرع</th>
                                        <th>رصيد افتتاحي</th>
                                        <th>العملة</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($treasuries as $t)
                                        <tr>
                                            <td>{{ $t->id }}</td>
                                            <td>{{ $t->name }}</td>
                                            <td>{{ $t->type === 'cashbox' ? 'خزنة' : 'بنك' }}</td>
                                            <td>{{ $t->branch->name ?? '—' }}</td>
                                            <td>{{ number_format((float)($t->opening_balance ?? 0), 2) }}</td>
                                            <td>{{ $t->currency ?? '—' }}</td>
                                            <td>
                                                @if($t->is_active)
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">غير نشط</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                                    @can('treasury-edit')
                                                    <a href="{{ route('admin.treasuries.edit', $t) }}" class="btn btn-sm btn-primary" title="تعديل"><i class="fas fa-edit"></i></a>
                                                    @endcan
                                                    @can('treasury-delete')
                                                    <form action="{{ route('admin.treasuries.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">لا توجد خزائن أو بنوك.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($treasuries->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $treasuries->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
