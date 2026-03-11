@extends('admin.layouts.master')

@section('page-title')
العروض والخصومات
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">العروض والخصومات</h4>
            @can('promotion-create')
                <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> إضافة عرض جديد
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
                            <th>النوع</th>
                            <th>القيمة</th>
                            <th>الفترة</th>
                            <th>الحد الأدنى للكمية</th>
                            <th>الحالة</th>
                            <th>عدد المنتجات</th>
                            <th>التحكم</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($promotions as $promotion)
                            <tr>
                                <td>{{ $promotion->id }}</td>
                                <td>{{ $promotion->name }}</td>
                                <td>{{ $promotion->type === 'percent' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</td>
                                <td>{{ number_format($promotion->value, 2) }}</td>
                                <td>
                                    @if($promotion->start_date || $promotion->end_date)
                                        {{ $promotion->start_date ? $promotion->start_date->format('Y-m-d') : 'بدون بداية' }}
                                        -
                                        {{ $promotion->end_date ? $promotion->end_date->format('Y-m-d') : 'بدون نهاية' }}
                                    @else
                                        مستمر
                                    @endif
                                </td>
                                <td>
                                    {{ $promotion->min_qty !== null ? number_format($promotion->min_qty, 2) : '-' }}
                                </td>
                                <td>
                                    <span class="badge {{ $promotion->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $promotion->is_active ? 'نشط' : 'متوقف' }}
                                    </span>
                                </td>
                                <td>{{ $promotion->items_count }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @can('promotion-edit')
                                            <a href="{{ route('admin.promotions.edit', $promotion) }}"
                                               class="btn btn-sm btn-warning">تعديل</a>
                                        @endcan
                                        @can('promotion-delete')
                                            <form action="{{ route('admin.promotions.destroy', $promotion) }}" method="POST"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا العرض؟');">
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
                                <td colspan="9" class="text-center">لا توجد عروض حالياً.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $promotions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

