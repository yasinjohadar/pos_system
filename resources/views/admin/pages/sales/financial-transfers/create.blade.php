@extends('admin.layouts.master')

@section('page-title')
    تحويل مالي جديد
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تحويل مالي جديد</h5>
            </div>
            <div>
                <a href="{{ route('admin.financial-transfers.index') }}" class="btn btn-secondary btn-sm">
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
                        <form action="{{ route('admin.financial-transfers.store') }}" method="POST" id="transfer-form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">المصدر <span class="text-danger">*</span></label>
                                    <select name="from_source" id="from_source" class="form-select" required>
                                        <option value="treasury" {{ old('from_source') === 'treasury' ? 'selected' : '' }}>خزنة / بنك</option>
                                        <option value="bank_account" {{ old('from_source') === 'bank_account' ? 'selected' : '' }}>حساب بنكي</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="from_treasury_wrap">
                                    <label class="form-label">الخزنة / البنك <span class="text-danger">*</span></label>
                                    <select name="from_treasury_id" id="from_treasury_id" class="form-select">
                                        <option value="">— اختر —</option>
                                        @foreach($treasuries as $t)
                                            <option value="{{ $t->id }}" {{ old('from_treasury_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->type === 'cashbox' ? 'خزنة' : 'بنك' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3 d-none" id="from_bank_wrap">
                                    <label class="form-label">الحساب البنكي <span class="text-danger">*</span></label>
                                    <select name="from_bank_account_id" id="from_bank_account_id" class="form-select">
                                        <option value="">— اختر —</option>
                                        @foreach($bankAccounts as $ba)
                                            <option value="{{ $ba->id }}" {{ old('from_bank_account_id') == $ba->id ? 'selected' : '' }}>{{ $ba->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الوجهة <span class="text-danger">*</span></label>
                                    <select name="to_source" id="to_source" class="form-select" required>
                                        <option value="treasury" {{ old('to_source') === 'treasury' ? 'selected' : '' }}>خزنة / بنك</option>
                                        <option value="bank_account" {{ old('to_source') === 'bank_account' ? 'selected' : '' }}>حساب بنكي</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3" id="to_treasury_wrap">
                                    <label class="form-label">الخزنة / البنك <span class="text-danger">*</span></label>
                                    <select name="to_treasury_id" id="to_treasury_id" class="form-select">
                                        <option value="">— اختر —</option>
                                        @foreach($treasuries as $t)
                                            <option value="{{ $t->id }}" {{ old('to_treasury_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ $t->type === 'cashbox' ? 'خزنة' : 'بنك' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3 d-none" id="to_bank_wrap">
                                    <label class="form-label">الحساب البنكي <span class="text-danger">*</span></label>
                                    <select name="to_bank_account_id" id="to_bank_account_id" class="form-select">
                                        <option value="">— اختر —</option>
                                        @foreach($bankAccounts as $ba)
                                            <option value="{{ $ba->id }}" {{ old('to_bank_account_id') == $ba->id ? 'selected' : '' }}>{{ $ba->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required min="0.01">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">تاريخ التحويل <span class="text-danger">*</span></label>
                                    <input type="date" name="transfer_date" class="form-control" value="{{ old('transfer_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">مرجع</label>
                                    <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> تسجيل التحويل</button>
                                <a href="{{ route('admin.financial-transfers.index') }}" class="btn btn-secondary">إلغاء</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function toggleFrom() {
        var src = document.getElementById('from_source').value;
        document.getElementById('from_treasury_wrap').classList.toggle('d-none', src !== 'treasury');
        document.getElementById('from_bank_wrap').classList.toggle('d-none', src !== 'bank_account');
        document.getElementById('from_treasury_id').required = src === 'treasury';
        document.getElementById('from_bank_account_id').required = src === 'bank_account';
    }
    function toggleTo() {
        var src = document.getElementById('to_source').value;
        document.getElementById('to_treasury_wrap').classList.toggle('d-none', src !== 'treasury');
        document.getElementById('to_bank_wrap').classList.toggle('d-none', src !== 'bank_account');
        document.getElementById('to_treasury_id').required = src === 'treasury';
        document.getElementById('to_bank_account_id').required = src === 'bank_account';
    }
    document.getElementById('from_source').addEventListener('change', toggleFrom);
    document.getElementById('to_source').addEventListener('change', toggleTo);
    toggleFrom();
    toggleTo();
});
</script>
@stop
