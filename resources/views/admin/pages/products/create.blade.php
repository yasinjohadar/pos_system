@extends('admin.layouts.master')

@section('page-title')
    إضافة منتج
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">إضافة منتج</h5>
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

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">التصنيف</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="">— اختر —</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="unit_id" class="form-label">الوحدة</label>
                                    <select class="form-select" id="unit_id" name="unit_id">
                                        <option value="">— اختر —</option>
                                        @foreach($units as $u)
                                            <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->symbol }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="barcode" class="form-label">الباركود</label>
                                <input type="text" class="form-control" id="barcode" name="barcode" value="{{ old('barcode') }}" placeholder="للبحث في نقطة البيع">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">الوصف</label>
                                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="base_price" class="form-label">السعر الأساسي <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="base_price" name="base_price" value="{{ old('base_price', 0) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cost_price" class="form-label">سعر التكلفة</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="cost_price" name="cost_price" value="{{ old('cost_price') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="min_stock_alert" class="form-label">حد تنبيه المخزون</label>
                                    <input type="number" min="0" class="form-control" id="min_stock_alert" name="min_stock_alert" value="{{ old('min_stock_alert', 0) }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="reorder_level" class="form-label">حد إعادة الطلب</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="reorder_level" name="reorder_level" value="{{ old('reorder_level') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="max_level" class="form-label">الحد الأقصى للمخزون</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="max_level" name="max_level" value="{{ old('max_level') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="image" class="form-label">الصورة</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">منتج نشط</label>
                                </div>
                            </div>
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
@stop
