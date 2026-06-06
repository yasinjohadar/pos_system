@extends('admin.layouts.master')

@section('page-title')
    نموذج جرد المخزون
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">جرد المخزون — {{ $warehouse->name }}</h5>
                    <a href="{{ route('admin.stock.inventory-count.index') }}" class="users-btn-secondary">
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

                <div class="users-form-card">
                    <div class="users-form-card__header">
                        <h6 class="users-form-card__title"><i class="fas fa-clipboard-check"></i> إدخال الأرصدة الفعلية</h6>
                    </div>
                    <form action="{{ route('admin.stock.inventory-count.store') }}" method="POST" class="users-form-card__body">
                        @csrf
                        <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">

                        <div class="users-form-group" style="max-width: 240px;">
                            <label for="count_date" class="users-form-label"><i class="fas fa-calendar"></i> تاريخ الجرد <span class="users-form-required">*</span></label>
                            <input type="date" class="users-form-input" id="count_date" name="count_date" value="{{ old('count_date', date('Y-m-d')) }}" required>
                        </div>

                        <p class="users-muted-text" style="margin-bottom: 1rem;">
                            أدخل الرصيد الفعلي بعد الجرد. سيتم إنشاء حركات تسوية تلقائياً للفرق بين الرصيد المحسوب والفعلي.
                        </p>

                        <div class="users-table-card users-table-card--nested">
                            <div class="table-responsive">
                                <table class="users-table">
                                    <thead>
                                        <tr>
                                            <th>المنتج</th>
                                            <th>الرصيد المحسوب</th>
                                            <th>حد التنبيه</th>
                                            <th style="width: 160px;">الرصيد الفعلي <span class="users-form-required">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($balances as $b)
                                            @php
                                                $isLow = $b->product && $b->product->min_stock_alert > 0 && (float) $b->quantity <= (float) $b->product->min_stock_alert;
                                            @endphp
                                            <tr class="{{ $isLow ? 'users-row--warning' : '' }}">
                                                <td>
                                                    <div class="users-user-cell">
                                                        <div class="users-avatar"><i class="fas fa-box"></i></div>
                                                        <span class="users-user-name" style="cursor: default;">{{ $b->product->name ?? '—' }}</span>
                                                    </div>
                                                </td>
                                                <td><span class="users-badge users-badge--role">{{ number_format($b->quantity, 2) }}</span></td>
                                                <td>{{ $b->product ? $b->product->min_stock_alert : '—' }}</td>
                                                <td>
                                                    <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $b->product_id }}">
                                                    <input type="number" step="0.0001" min="0" class="users-form-input" name="items[{{ $loop->index }}][actual_quantity]" value="{{ old('items.'.$loop->index.'.actual_quantity', $b->quantity) }}" required>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="users-empty">لا توجد أرصدة في هذا المخزن — أضف حركات إدخال أولاً</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if ($balances->count() > 0)
                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit"><i class="fas fa-check"></i> تنفيذ الجرد</button>
                                <a href="{{ route('admin.stock.inventory-count.index') }}" class="users-btn-secondary">إلغاء</a>
                            </div>
                        @endif
                    </form>
                </div>

            </div>
        </div>
    </div>
@stop
