@extends('admin.layouts.master')

@section('page-title')
    تعديل الوحدة
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تعديل الوحدة: {{ $unit->name }}</h5>
            </div>
            <div>
                <a href="{{ route('admin.units.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
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
                        <form action="{{ route('admin.units.update', $unit) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $unit->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="symbol" class="form-label">الرمز</label>
                                <input type="text" class="form-control" id="symbol" name="symbol" value="{{ old('symbol', $unit->symbol) }}">
                            </div>
                            <div class="mb-3">
                                <label for="base_unit_id" class="form-label">الوحدة الأساسية</label>
                                <select class="form-select" id="base_unit_id" name="base_unit_id">
                                    <option value="">— وحدة أساسية —</option>
                                    @foreach($baseUnits as $u)
                                        <option value="{{ $u->id }}" {{ old('base_unit_id', $unit->base_unit_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="conversion_factor" class="form-label">معامل التحويل</label>
                                <input type="number" step="0.0001" class="form-control" id="conversion_factor" name="conversion_factor" value="{{ old('conversion_factor', $unit->conversion_factor) }}">
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $unit->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">وحدة نشطة</label>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> حفظ</button>
                                <a href="{{ route('admin.units.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
