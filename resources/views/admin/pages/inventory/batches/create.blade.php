@extends('admin.layouts.master')

@section('page-title')
    دفعة منتج جديدة
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">دفعة منتج جديدة</h5>
                    <a href="{{ route('admin.product-batches.index') }}" class="users-btn-secondary">
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

                <form action="{{ route('admin.product-batches.store') }}" method="POST">
                    @csrf
                    <div class="users-form-layout">
                        @include('admin.components.premium.form-aside', [
                            'icon' => 'fa-boxes',
                            'title' => 'دفعة جديدة',
                            'text' => 'سجّل دفعة منتج للتتبع بالكمية وتاريخ الانتهاء.',
                            'tips' => ['رقم الدفعة يجب أن يكون فريداً لكل منتج', 'الكمية الافتتاحية = الكمية الحالية'],
                        ])
                        <div class="users-form-card">
                            <div class="users-form-card__header">
                                <h6 class="users-form-card__title"><i class="fas fa-boxes"></i> بيانات الدفعة</h6>
                            </div>
                            <div class="users-form-card__body">
                                <div class="users-form-grid">
                                    <div class="users-form-group">
                                        <label class="users-form-label">المنتج <span class="users-form-required">*</span></label>
                                        <select name="product_id" class="users-form-select @error('product_id') is-invalid @enderror" required>
                                            <option value="">— اختر —</option>
                                            @foreach ($products as $p)
                                                <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('product_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">المخزن <span class="users-form-required">*</span></label>
                                        <select name="warehouse_id" class="users-form-select @error('warehouse_id') is-invalid @enderror" required>
                                            <option value="">— اختر —</option>
                                            @foreach ($warehouses as $w)
                                                <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('warehouse_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">رقم الدفعة <span class="users-form-required">*</span></label>
                                        <input type="text" name="batch_number" class="users-form-input @error('batch_number') is-invalid @enderror" value="{{ old('batch_number') }}" required>
                                        @error('batch_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">تاريخ الاستلام <span class="users-form-required">*</span></label>
                                        <input type="date" name="received_date" class="users-form-input @error('received_date') is-invalid @enderror" value="{{ old('received_date', date('Y-m-d')) }}" required>
                                        @error('received_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">تاريخ الانتهاء</label>
                                        <input type="date" name="expiry_date" class="users-form-input @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date') }}">
                                        @error('expiry_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">الكمية الافتتاحية <span class="users-form-required">*</span></label>
                                        <input type="number" name="initial_quantity" class="users-form-input @error('initial_quantity') is-invalid @enderror" step="any" min="0.0001" value="{{ old('initial_quantity') }}" required>
                                        @error('initial_quantity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">سعر التكلفة</label>
                                        <input type="number" name="cost_price" class="users-form-input @error('cost_price') is-invalid @enderror" step="0.01" min="0" value="{{ old('cost_price') }}">
                                        @error('cost_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group users-form-group--full">
                                        <label class="users-form-label">ملاحظات</label>
                                        <textarea name="notes" class="users-form-input" rows="2">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                                <div class="users-form-actions">
                                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> حفظ</button>
                                    <a href="{{ route('admin.product-batches.index') }}" class="users-btn-secondary">إلغاء</a>
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
