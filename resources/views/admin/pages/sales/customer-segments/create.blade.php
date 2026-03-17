@extends('admin.layouts.master')

@section('page-title')
    إضافة شريحة عملاء
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">إضافة شريحة عملاء</h4>
            <a href="{{ route('admin.customer-segments.index') }}" class="btn btn-secondary">رجوع لشرائح العملاء</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.customer-segments.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم (عربي)</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الاسم (إنجليزي)</label>
                            <input type="text" name="name_en" value="{{ old('name_en') }}" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">اللون</label>
                            <input type="color" name="color" value="{{ old('color', '#6366f1') }}" class="form-control form-control-color" style="height: 38px;">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">نشط</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
