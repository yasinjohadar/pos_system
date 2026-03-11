@extends('admin.layouts.master')

@section('page-title')
قوائم الأسعار
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">قوائم الأسعار</h4>
            @can('price-list-create')
                <a href="{{ route('admin.price-lists.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة قائمة أسعار جديدة
                </a>
            @endcan
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الوصف</th>
                            <th>الحالة</th>
                            <th>عدد المنتجات</th>
                            <th>التحكم</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($priceLists as $list)
                            <tr>
                                <td>{{ $list->id }}</td>
                                <td>{{ $list->name }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($list->description, 60) }}</td>
                                <td>
                                    <span class="badge {{ $list->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $list->is_active ? 'نشطة' : 'متوقفة' }}
                                    </span>
                                </td>
                                <td>{{ $list->items_count }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @can('price-list-edit')
                                            <a href="{{ route('admin.price-lists.edit', $list) }}"
                                               class="btn btn-sm btn-warning">تعديل</a>
                                        @endcan
                                        @can('price-list-delete')
                                            <form action="{{ route('admin.price-lists.destroy', $list) }}" method="POST"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه القائمة؟');">
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
                                <td colspan="6" class="text-center">لا توجد قوائم أسعار حالياً.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $priceLists->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

