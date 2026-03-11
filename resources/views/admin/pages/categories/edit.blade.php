@extends('admin.layouts.master')

@section('page-title')
    تعديل التصنيف
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تعديل التصنيف: {{ $category->name }}</h5>
            </div>
            <div>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="parent_id" class="form-label">التصنيف الأب</label>
                                <select class="form-select" id="parent_id" name="parent_id">
                                    <option value="">— لا يوجد —</option>
                                    @foreach($parentCategories as $c)
                                        <option value="{{ $c->id }}" {{ old('parent_id', $category->parent_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">الوصف</label>
                                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $category->description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">الصورة</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                @if($category->image)
                                    <small class="text-muted">الحالية: {{ basename($category->image) }}</small>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="order" class="form-label">الترتيب</label>
                                <input type="number" class="form-control" id="order" name="order" value="{{ old('order', $category->order) }}" min="0">
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">تصنيف نشط</label>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> حفظ</button>
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
