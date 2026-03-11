@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المنتج
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تفاصيل المنتج: {{ $product->name }}</h5>
            </div>
            <div class="d-flex gap-2">
                @can('product-edit')
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-sm">تعديل</a>
                @endcan
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        @if($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image))
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height:180px">
                                <span class="text-muted">لا توجد صورة</span>
                            </div>
                        @endif
                        <table class="table table-borderless mb-0">
                            <tr><td class="text-muted" width="140">الاسم:</td><td>{{ $product->name }}</td></tr>
                            <tr><td class="text-muted">الباركود:</td><td>{{ $product->barcode ?? '—' }}</td></tr>
                            <tr><td class="text-muted">التصنيف:</td><td>{{ $product->category->name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">الوحدة:</td><td>{{ $product->unit->name ?? '—' }} ({{ $product->unit->symbol ?? '—' }})</td></tr>
                            <tr><td class="text-muted">السعر الأساسي:</td><td>{{ number_format($product->base_price, 2) }}</td></tr>
                            <tr><td class="text-muted">سعر التكلفة:</td><td>{{ $product->cost_price !== null ? number_format($product->cost_price, 2) : '—' }}</td></tr>
                            <tr><td class="text-muted">حد تنبيه المخزون:</td><td>{{ $product->min_stock_alert }}</td></tr>
                            <tr><td class="text-muted">الحالة:</td>
                                <td>@if($product->is_active)<span class="badge bg-success">نشط</span>@else<span class="badge bg-danger">غير نشط</span>@endif</td>
                            </tr>
                        </table>
                        @if($product->description)
                            <p class="mt-2 mb-0"><strong>الوصف:</strong><br>{{ $product->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header"><h6 class="mb-0">أسعار إضافية (حسب الفرع ونوع السعر)</h6></div>
                    <div class="card-body">
                        @if($product->prices->count())
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الفرع</th>
                                            <th>نوع السعر</th>
                                            <th>القيمة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->prices as $p)
                                            <tr>
                                                <td>{{ $p->branch_id ? $p->branch->name : 'افتراضي (جميع الفروع)' }}</td>
                                                <td>{{ \App\Models\ProductPrice::PRICE_TYPES[$p->price_type] ?? $p->price_type }}</td>
                                                <td>{{ number_format($p->value, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mb-0 text-muted">لا توجد أسعار إضافية. يُستخدم السعر الأساسي لجميع الفروع.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
