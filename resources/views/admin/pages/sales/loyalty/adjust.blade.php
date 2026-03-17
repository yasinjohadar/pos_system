@extends('admin.layouts.master')

@section('page-title')
    تعديل نقاط عميل
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center my-4">
            <h4 class="mb-0">تعديل نقاط عميل</h4>
            <a href="{{ route('admin.loyalty.index') }}" class="btn btn-secondary">رجوع لنقاط الولاء</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.loyalty.adjust') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">العميل</label>
                            <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                <option value="">-- اختر العميل --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" data-points="{{ $c->loyalty_points }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} (رصيد: {{ $c->loyalty_points }} نقطة)</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">عدد النقاط (موجب للإضافة، سالب للخصم)</label>
                            <input type="number" name="points" value="{{ old('points') }}" class="form-control @error('points') is-invalid @enderror" required placeholder="مثال: 50 أو -20">
                            @error('points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السبب / الوصف</label>
                        <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="2" required placeholder="مثال: تصحيح رصيد، مكافأة، إلخ">{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">تطبيق التعديل</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
