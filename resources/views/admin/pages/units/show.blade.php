@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الوحدة
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تفاصيل الوحدة: {{ $unit->name }}</h5>
            </div>
            <div class="d-flex gap-2">
                @can('unit-edit')
                <a href="{{ route('admin.units.edit', $unit) }}" class="btn btn-primary btn-sm">تعديل</a>
                @endcan
                <a href="{{ route('admin.units.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr><td class="text-muted" width="140">الاسم:</td><td>{{ $unit->name }}</td></tr>
                            <tr><td class="text-muted">الرمز:</td><td>{{ $unit->symbol ?? '—' }}</td></tr>
                            <tr><td class="text-muted">الوحدة الأساسية:</td><td>{{ $unit->baseUnit->name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">معامل التحويل:</td><td>{{ $unit->conversion_factor }}</td></tr>
                            <tr><td class="text-muted">الحالة:</td>
                                <td>@if($unit->is_active)<span class="badge bg-success">نشط</span>@else<span class="badge bg-danger">غير نشط</span>@endif</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header"><h6 class="mb-0">المنتجات ({{ $unit->products->count() }})</h6></div>
                    <div class="card-body">
                        @if($unit->products->count())
                            <ul class="list-group list-group-flush">
                                @foreach($unit->products->take(10) as $p)
                                    <li class="list-group-item">
                                        <a href="{{ route('admin.products.show', $p) }}">{{ $p->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            @if($unit->products->count() > 10)
                                <p class="mb-0 mt-2 text-muted">و {{ $unit->products->count() - 10 }} منتج آخر</p>
                            @endif
                        @else
                            <p class="mb-0 text-muted">لا توجد منتجات بهذه الوحدة.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
