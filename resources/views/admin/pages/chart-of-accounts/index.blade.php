@extends('admin.layouts.master')

@section('page-title')
    شجرة الحسابات
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">شجرة الحسابات</h4>
            @can('chart-of-account-create')
                <a href="{{ route('admin.chart-of-accounts.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة حساب</a>
            @endcan
        </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>الكود</th>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>عدد الحركات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $a)
                            <tr>
                                <td>{{ $a->code }}</td>
                                <td>{{ $a->name }}</td>
                                <td>{{ $a->type }}</td>
                                <td><span class="badge {{ $a->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $a->is_active ? 'نشط' : 'غير نشط' }}</span></td>
                                <td>{{ $a->journal_entry_lines_count }}</td>
                                <td>
                                    @can('chart-of-account-edit')
                                        <a href="{{ route('admin.chart-of-accounts.edit', $a) }}" class="btn btn-sm btn-warning">تعديل</a>
                                    @endcan
                                    @can('chart-of-account-delete')
                                        <form action="{{ route('admin.chart-of-accounts.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف هذا الحساب؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">لا توجد حسابات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-3">{{ $accounts->links() }}</div>
            </div>
        </div>
    </div>
</div>
@stop
