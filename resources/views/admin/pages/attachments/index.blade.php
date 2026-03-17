@extends('admin.layouts.master')

@section('page-title')
    المرفقات
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto"><h5 class="page-title fs-21 mb-1">المرفقات</h5></div>
        </div>
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">نوع السجل</label>
                        <select name="attachable_type" class="form-select">
                            <option value="">الكل</option>
                            @foreach($attachableTypes as $class => $label)
                                <option value="{{ $class }}" {{ request('attachable_type') == $class ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نوع المرفق</label>
                        <select name="type" class="form-select">
                            <option value="">الكل</option>
                            @foreach($types as $k => $v)
                                <option value="{{ $k }}" {{ request('type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
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
                            <th>الملف</th>
                            <th>نوع السجل</th>
                            <th>المعرف</th>
                            <th>نوع المرفق</th>
                            <th>رفع بواسطة</th>
                            <th>التاريخ</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attachments as $a)
                            <tr>
                                <td>
                                    <a href="{{ asset('storage/' . $a->path) }}" target="_blank">{{ $a->original_filename }}</a>
                                    <small class="text-muted">({{ number_format($a->size / 1024, 1) }} KB)</small>
                                </td>
                                <td>{{ $attachableTypes[$a->attachable_type] ?? class_basename($a->attachable_type) }}</td>
                                <td>{{ $a->attachable_id }}</td>
                                <td>{{ $types[$a->type] ?? $a->type }}</td>
                                <td>{{ $a->uploadedBy->name ?? '—' }}</td>
                                <td>{{ $a->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @can('attachment-delete')
                                        <form action="{{ route('admin.attachments.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف المرفق؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">لا توجد مرفقات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-3">{{ $attachments->withQueryString()->links() }}</div>
            </div>
        </div>
    </div>
</div>
@stop
