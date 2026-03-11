@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التصنيف
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تفاصيل التصنيف: {{ $category->name }}</h5>
            </div>
            <div class="d-flex gap-2">
                @can('category-edit')
                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary btn-sm">تعديل</a>
                @endcan
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr><td class="text-muted" width="120">الاسم:</td><td>{{ $category->name }}</td></tr>
                            <tr><td class="text-muted">الرابط:</td><td>{{ $category->slug }}</td></tr>
                            <tr><td class="text-muted">التصنيف الأب:</td><td>{{ $category->parent->name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">الوصف:</td><td>{{ $category->description ?? '—' }}</td></tr>
                            <tr><td class="text-muted">الحالة:</td>
                                <td>@if($category->is_active)<span class="badge bg-success">نشط</span>@else<span class="badge bg-danger">غير نشط</span>@endif</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header"><h6 class="mb-0">المنتجات ({{ $category->products->count() }})</h6></div>
                    <div class="card-body">
                        @if($category->products->count())
                            <ul class="list-group list-group-flush">
                                @foreach($category->products->take(10) as $p)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <a href="{{ route('admin.products.show', $p) }}">{{ $p->name }}</a>
                                        <span>{{ number_format($p->base_price, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            @if($category->products->count() > 10)
                                <p class="mb-0 mt-2 text-muted">و {{ $category->products->count() - 10 }} منتج آخر</p>
                            @endif
                        @else
                            <p class="mb-0 text-muted">لا توجد منتجات في هذا التصنيف.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
