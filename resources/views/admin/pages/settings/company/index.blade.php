@extends('admin.layouts.master')

@section('page-title')
    إعدادات الشركة
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="users-header">
                    <h5 class="users-page-title">إعدادات الشركة</h5>
                </div>

                <form action="{{ route('admin.settings.company.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="users-form-layout">
                        @include('admin.components.premium.form-aside', [
                            'icon' => 'fa-building',
                            'title' => 'بيانات الشركة',
                            'text' => 'تظهر هذه البيانات في الفواتير والتقارير المطبوعة.',
                            'tips' => ['الشعار يُحفظ بصيغة JPG أو PNG', 'الضريبة الافتراضية تُطبّق على الفواتير الجديدة', 'تذييل الفاتورة يظهر أسفل كل طباعة'],
                        ])

                        <div class="users-form-card">
                            <div class="users-form-card__header">
                                <h6 class="users-form-card__title"><i class="fas fa-cog"></i> الإعدادات العامة</h6>
                            </div>
                            <div class="users-form-card__body">
                                <div class="users-form-grid">
                                    <div class="users-form-group">
                                        <label for="company_name" class="users-form-label"><i class="fas fa-font"></i> اسم الشركة <span class="users-form-required">*</span></label>
                                        <input type="text" name="company_name" id="company_name" class="users-form-input @error('company_name') is-invalid @enderror"
                                            value="{{ old('company_name', $settings['company_name'] ?? '') }}" required>
                                        @error('company_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="users-form-group">
                                        <label for="tax_number" class="users-form-label"><i class="fas fa-id-card"></i> الرقم الضريبي</label>
                                        <input type="text" name="tax_number" id="tax_number" class="users-form-input @error('tax_number') is-invalid @enderror"
                                            value="{{ old('tax_number', $settings['tax_number'] ?? '') }}" dir="ltr">
                                        @error('tax_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="users-form-group">
                                        <label for="default_currency" class="users-form-label"><i class="fas fa-coins"></i> العملة الافتراضية <span class="users-form-required">*</span></label>
                                        <input type="text" name="default_currency" id="default_currency" class="users-form-input @error('default_currency') is-invalid @enderror"
                                            value="{{ old('default_currency', $settings['default_currency'] ?? 'SAR') }}" dir="ltr" required>
                                        @error('default_currency')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="users-form-group">
                                        <label for="default_tax_id" class="users-form-label"><i class="fas fa-percent"></i> الضريبة الافتراضية</label>
                                        <select name="default_tax_id" id="default_tax_id" class="users-form-select @error('default_tax_id') is-invalid @enderror">
                                            <option value="">— بدون —</option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}" {{ old('default_tax_id', $settings['default_tax_id'] ?? '') == $tax->id ? 'selected' : '' }}>
                                                    {{ $tax->name }} ({{ $tax->type === 'percent' ? $tax->rate . '%' : number_format($tax->rate, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('default_tax_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="users-form-group">
                                        <label for="company_logo" class="users-form-label"><i class="fas fa-image"></i> شعار الشركة</label>
                                        @if ($logoUrl)
                                            <div class="mb-2">
                                                <img src="{{ $logoUrl }}" alt="شعار الشركة" style="max-height: 80px; border-radius: 8px;">
                                            </div>
                                        @endif
                                        <input type="file" name="company_logo" id="company_logo" class="users-form-input @error('company_logo') is-invalid @enderror" accept="image/*">
                                        @error('company_logo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="users-form-group users-form-group--full">
                                        <label for="invoice_footer" class="users-form-label"><i class="fas fa-align-right"></i> تذييل الفاتورة</label>
                                        <textarea name="invoice_footer" id="invoice_footer" rows="3" class="users-form-input @error('invoice_footer') is-invalid @enderror">{{ old('invoice_footer', $settings['invoice_footer'] ?? '') }}</textarea>
                                        @error('invoice_footer')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="users-form-actions">
                                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> حفظ الإعدادات</button>
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
