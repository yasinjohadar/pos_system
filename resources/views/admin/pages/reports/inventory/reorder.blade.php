@extends('admin.layouts.master')

@section('page-title')
تنبيهات إعادة طلب المخزون
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تنبيهات إعادة طلب المخزون</h5>
                <span class="text-muted fs-12">قائمة المنتجات التي وصل رصيدها إلى حد إعادة الطلب أو أقل.</span>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>التصنيف</th>
                            <th>الرصيد الحالي (إجمالي كل المخازن)</th>
                            <th>حد إعادة الطلب</th>
                            <th>الحد الأقصى (إن وُجد)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($rows as $row)
                            @php
                                $product = $row->product;
                            @endphp
                            <tr>
                                <td>{{ $product?->name }}</td>
                                <td>{{ $product?->category?->name }}</td>
                                <td>{{ number_format($row->total_qty, 2) }}</td>
                                <td>{{ number_format($product->reorder_level, 2) }}</td>
                                <td>
                                    {{ $product->max_level !== null ? number_format($product->max_level, 2) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">لا توجد منتجات تحتاج إلى إعادة طلب حالياً.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

