@extends('admin.layouts.master')

@section('page-title')
    إضافة حساب
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">إضافة حساب</h4>
            <a href="{{ route('admin.chart-of-accounts.index') }}" class="btn btn-secondary">رجوع</a>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.chart-of-accounts.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">كود الحساب</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">اسم الحساب</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">النوع</label>
                            <select name="type" class="form-select" required>
                                <option value="asset" {{ old('type') == 'asset' ? 'selected' : '' }}>أصول</option>
                                <option value="liability" {{ old('type') == 'liability' ? 'selected' : '' }}>خصوم</option>
                                <option value="equity" {{ old('type') == 'equity' ? 'selected' : '' }}>حقوق ملكية</option>
                                <option value="revenue" {{ old('type') == 'revenue' ? 'selected' : '' }}>إيرادات</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>مصروفات</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الحساب الأب</label>
                            <select name="parent_id" class="form-select">
                                <option value="">بدون</option>
                                @foreach($parents as $p)
                                    <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->code }} - {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
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
