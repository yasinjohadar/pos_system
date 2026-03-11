@extends('admin.layouts.master')

@section('page-title')
    تعديل المنتج
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تعديل المنتج: {{ $product->name }}</h5>
            </div>
            <div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">التصنيف</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="">— اختر —</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="unit_id" class="form-label">الوحدة</label>
                                    <select class="form-select" id="unit_id" name="unit_id">
                                        <option value="">— اختر —</option>
                                        @foreach($units as $u)
                                            <option value="{{ $u->id }}" {{ old('unit_id', $product->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="barcode" class="form-label">الباركود</label>
                                <input type="text" class="form-control" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">الوصف</label>
                                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $product->description) }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="base_price" class="form-label">السعر الأساسي <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="base_price" name="base_price" value="{{ old('base_price', $product->base_price) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cost_price" class="form-label">سعر التكلفة</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="min_stock_alert" class="form-label">حد تنبيه المخزون</label>
                                    <input type="number" min="0" class="form-control" id="min_stock_alert" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="reorder_level" class="form-label">حد إعادة الطلب</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="max_level" class="form-label">الحد الأقصى للمخزون</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="max_level" name="max_level" value="{{ old('max_level', $product->max_level) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="image" class="form-label">الصورة</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    @if($product->image)
                                        <small class="text-muted">الحالية: {{ basename($product->image) }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">منتج نشط</label>
                                </div>
                            </div>

                            <h6 class="mb-2">أسعار إضافية (حسب الفرع ونوع السعر)</h6>
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="prices-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>الفرع</th>
                                            <th>نوع السعر</th>
                                            <th>القيمة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $pricesData = old('prices', $product->prices->count() ? $product->prices : [['branch_id' => null, 'price_type' => 'retail', 'value' => '']]);
                                        @endphp
                                        @foreach($pricesData as $i => $priceRow)
                                            @php
                                                $branchId = is_object($priceRow) ? $priceRow->branch_id : ($priceRow['branch_id'] ?? '');
                                                $priceType = is_object($priceRow) ? $priceRow->price_type : ($priceRow['price_type'] ?? 'retail');
                                                $value = is_object($priceRow) ? $priceRow->value : ($priceRow['value'] ?? '');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm" name="prices[{{ $i }}][branch_id]">
                                                        <option value="">افتراضي (جميع الفروع)</option>
                                                        @foreach($branches as $b)
                                                            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="prices[{{ $i }}][price_type]">
                                                        @foreach(\App\Models\ProductPrice::PRICE_TYPES as $k => $v)
                                                            <option value="{{ $k }}" {{ $priceType == $k ? 'selected' : '' }}>{{ $v }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="prices[{{ $i }}][value]" value="{{ $value }}" placeholder="0.00">
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr id="price-row-template" style="display:none;">
                                            <td>
                                                <select class="form-select form-select-sm" name="prices[__INDEX__][branch_id]">
                                                    <option value="">افتراضي</option>
                                                    @foreach($branches as $b)
                                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm" name="prices[__INDEX__][price_type]">
                                                    @foreach(\App\Models\ProductPrice::PRICE_TYPES as $k => $v)
                                                        <option value="{{ $k }}">{{ $v }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="prices[__INDEX__][value]" placeholder="0.00">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="add-price-row">
                                <i class="fas fa-plus me-1"></i> إضافة سعر
                            </button>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> حفظ</button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('add-price-row')?.addEventListener('click', function () {
    var tbody = document.querySelector('#prices-table tbody');
    var template = document.getElementById('price-row-template');
    if (!tbody || !template) return;
    var index = tbody.querySelectorAll('tr:not(#price-row-template)').length;
    var html = template.outerHTML.replace(/__INDEX__/g, index).replace('style="display:none;"', '').replace('id="price-row-template"', '');
    template.insertAdjacentHTML('beforebegin', html);
});
</script>
@endpush
@stop
