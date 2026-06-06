@extends('admin.layouts.master')

@section('page-title')
    تسوية بنكية جديدة
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تسوية بنكية جديدة</h5>
                    <a href="{{ route('admin.bank-reconciliations.index') }}" class="users-btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>

                @include('admin.components.premium.flash')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.bank-reconciliations.store') }}" method="POST">
                    @csrf
                    <div class="users-form-layout">
                        @include('admin.components.premium.form-aside', [
                            'icon' => 'fa-university',
                            'title' => 'تسوية بنكية',
                            'text' => 'قارن رصيد كشف الحساب البنكي مع رصيد الدفاتر.',
                            'tips' => ['اختر الحساب البنكي الصحيح', 'أدخل تاريخ وقيمة الكشف كما في البنك'],
                        ])
                        <div class="users-form-card">
                            <div class="users-form-card__header">
                                <h6 class="users-form-card__title"><i class="fas fa-university"></i> بيانات التسوية</h6>
                            </div>
                            <div class="users-form-card__body">
                                <div class="users-form-grid">
                                    <div class="users-form-group">
                                        <label for="bank_account_id" class="users-form-label">الحساب البنكي <span class="users-form-required">*</span></label>
                                        <select name="bank_account_id" id="bank_account_id" class="users-form-select @error('bank_account_id') is-invalid @enderror" required>
                                            <option value="">— اختر —</option>
                                            @foreach ($bankAccounts as $ba)
                                                <option value="{{ $ba->id }}" {{ old('bank_account_id') == $ba->id ? 'selected' : '' }}>{{ $ba->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('bank_account_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label for="statement_date" class="users-form-label">تاريخ الكشف <span class="users-form-required">*</span></label>
                                        <input type="date" name="statement_date" id="statement_date" class="users-form-input @error('statement_date') is-invalid @enderror"
                                            value="{{ old('statement_date', date('Y-m-d')) }}" required>
                                        @error('statement_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label for="statement_balance" class="users-form-label">رصيد الكشف <span class="users-form-required">*</span></label>
                                        <input type="number" name="statement_balance" id="statement_balance" step="0.01" class="users-form-input @error('statement_balance') is-invalid @enderror"
                                            value="{{ old('statement_balance') }}" required>
                                        @error('statement_balance')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group users-form-group--full">
                                        <label for="notes" class="users-form-label">ملاحظات</label>
                                        <textarea name="notes" id="notes" rows="2" class="users-form-input">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                                <div class="users-form-actions">
                                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> إنشاء التسوية</button>
                                    <a href="{{ route('admin.bank-reconciliations.index') }}" class="users-btn-secondary">إلغاء</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
