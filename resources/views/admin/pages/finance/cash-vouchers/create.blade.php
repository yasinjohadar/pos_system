@extends('admin.layouts.master')

@section('page-title')
    سند قبض / صرف جديد
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">سند قبض / صرف جديد</h5>
            </div>
            <div>
                <a href="{{ route('admin.cash-vouchers.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>يرجى تصحيح الأخطاء التالية:</strong>
                        <ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form action="{{ route('admin.cash-vouchers.store') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label">النوع <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="receipt" {{ old('type') === 'payment' ? '' : 'selected' }}>قبض</option>
                                    <option value="payment" {{ old('type') === 'payment' ? 'selected' : '' }}>صرف</option>
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">التاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', date('Y-m-d')) }}" required>
                                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" required>
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الخزنة</label>
                                <select name="treasury_id" class="form-select @error('treasury_id') is-invalid @enderror">
                                    <option value="">— اختياري —</option>
                                    @foreach($treasuries as $t)
                                        <option value="{{ $t->id }}" {{ old('treasury_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->type === 'cashbox' ? 'خزنة' : 'بنك' }})</option>
                                    @endforeach
                                </select>
                                @error('treasury_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الحساب البنكي</label>
                                <select name="bank_account_id" class="form-select @error('bank_account_id') is-invalid @enderror">
                                    <option value="">— اختياري —</option>
                                    @foreach($bankAccounts as $ba)
                                        <option value="{{ $ba->id }}" {{ old('bank_account_id') == $ba->id ? 'selected' : '' }}>{{ $ba->name }}</option>
                                    @endforeach
                                </select>
                                @error('bank_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">العملة</label>
                                <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency') }}" placeholder="مثال: SAR">
                                @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الفئة</label>
                                <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') }}" placeholder="مصروف، إيراد آخر ...">
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">البيان</label>
                                <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}">
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> حفظ</button>
                                <a href="{{ route('admin.cash-vouchers.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

