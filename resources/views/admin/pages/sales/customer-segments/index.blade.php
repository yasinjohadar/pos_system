@extends('admin.layouts.master')

@section('page-title')
    شرائح العملاء
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">شرائح العملاء</h4>
            @can('customer-segment-create')
                <a href="{{ route('admin.customer-segments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة شريحة
                </a>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الوصف</th>
                            <th>اللون</th>
                            <th>عدد العملاء</th>
                            <th>الحالة</th>
                            <th>التحكم</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($segments as $segment)
                            <tr>
                                <td>{{ $segment->id }}</td>
                                <td>{{ $segment->name }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($segment->description, 50) ?: '—' }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $segment->color }}; color: #fff;">{{ $segment->color }}</span>
                                </td>
                                <td>{{ $segment->customers_count }}</td>
                                <td>
                                    <span class="badge {{ $segment->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $segment->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @can('customer-segment-edit')
                                            <a href="{{ route('admin.customer-segments.edit', $segment) }}" class="btn btn-sm btn-warning">تعديل</a>
                                        @endcan
                                        @can('customer-segment-delete')
                                            <form action="{{ route('admin.customer-segments.destroy', $segment) }}" method="POST"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الشريحة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">لا توجد شرائح عملاء حالياً.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $segments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop
