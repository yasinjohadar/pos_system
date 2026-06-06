@extends('admin.layouts.master')

@section('page-title')
    تعديل دفعة {{ $productBatch->batch_number }}
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تعديل دفعة: {{ $productBatch->batch_number }}</h5>
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

                <form action="{{ route('admin.product-batches.update', $productBatch) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="users-form-layout">
                        @include('admin.components.premium.form-aside', [
                            'icon' => 'fa-boxes',
                            'title' => 'تعديل الدفعة',
                            'text' => 'تحديث بيانات الدفعة. لا يمكن تغيير المنتج أو الكمية من هنا.',
                            'tips' => ['الكمية الحالية: ' . number_format($productBatch->current_quantity, 4)],
                        ])
                        <div class="users-form-card">
                            <div class="users-form-card__header">
                                <h6 class="users-form-card__title"><i class="fas fa-boxes"></i> بيانات الدفعة</h6>
                            </div>
                            <div class="users-form-card__body">
                                <div class="users-form-grid">
                                    <div class="users-form-group">
                                        <label class="users-form-label">المنتج</label>
                                        <input type="text" class="users-form-input" value="{{ $productBatch->product->name ?? '—' }}" disabled>
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">المخزن</label>
                                        <input type="text" class="users-form-input" value="{{ $productBatch->warehouse->name ?? '—' }}" disabled>
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">رقم الدفعة <span class="users-form-required">*</span></label>
                                        <input type="text" name="batch_number" class="users-form-input @error('batch_number') is-invalid @enderror" value="{{ old('batch_number', $productBatch->batch_number) }}" required>
                                        @error('batch_number')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">تاريخ الانتهاء</label>
                                        <input type="date" name="expiry_date" class="users-form-input @error('expiry_date') is-invalid @enderror"
                                            value="{{ old('expiry_date', $productBatch->expiry_date?->format('Y-m-d')) }}">
                                        @error('expiry_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">سعر التكلفة</label>
                                        <input type="number" name="cost_price" class="users-form-input @error('cost_price') is-invalid @enderror" step="0.01" min="0"
                                            value="{{ old('cost_price', $productBatch->cost_price) }}">
                                        @error('cost_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group users-form-group--full">
                                        <label class="users-form-label">ملاحظات</label>
                                        <textarea name="notes" class="users-form-input" rows="2">{{ old('notes', $productBatch->notes) }}</textarea>
                                    </div>
                                </div>
                                <div class="users-form-actions">
                                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> حفظ التعديلات</button>
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
