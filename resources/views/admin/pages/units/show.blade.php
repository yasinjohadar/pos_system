@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الوحدة
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تفاصيل الوحدة: {{ $unit->name }}</h5>
                    <div class="users-header-actions">
                        @can('unit-edit')
                            <a href="{{ route('admin.units.edit', $unit) }}" class="users-btn-edit">
                                <i class="fas fa-edit"></i>
                                تعديل الوحدة
                            </a>
                        @endcan
                        <a href="{{ route('admin.units.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title">
                                <i class="fas fa-ruler-combined"></i>
                                بيانات الوحدة
                            </h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-profile">
                                <div class="users-avatar">
                                    <i class="fas fa-ruler-combined"></i>
                                </div>
                                <div>
                                    <h6 class="users-detail-profile__name">{{ $unit->name }}</h6>
                                    <span class="users-detail-profile__code">
                                        {{ $unit->symbol ? 'الرمز: ' . $unit->symbol : 'بدون رمز' }}
                                    </span>
                                </div>
                            </div>

                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-layer-group"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الوحدة الأساسية</span>
                                        <div class="users-detail-item__value">
                                            @if ($unit->baseUnit)
                                                <a href="{{ route('admin.units.show', $unit->baseUnit) }}" class="users-email-link">
                                                    {{ $unit->baseUnit->name }}
                                                </a>
                                            @else
                                                <span class="users-muted-text">وحدة أساسية</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-calculator"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">معامل التحويل</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-badge users-badge--role">{{ number_format($unit->conversion_factor, 4) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-toggle-on"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة النشطة</span>
                                        <div class="users-detail-item__value">
                                            @can('unit-edit')
                                                <label class="users-toggle">
                                                    <input type="checkbox"
                                                        class="users-toggle-input"
                                                        id="unit-show-toggle"
                                                        data-toggle-url="{{ route('admin.units.toggle-status', $unit) }}"
                                                        {{ $unit->is_active ? 'checked' : '' }}>
                                                    <span class="users-toggle-track">
                                                        <span class="users-toggle-thumb"></span>
                                                    </span>
                                                    <span class="users-toggle-label">
                                                        {{ $unit->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </label>
                                            @else
                                                @if ($unit->is_active)
                                                    <span class="users-badge users-badge--active">نشط</span>
                                                @else
                                                    <span class="users-badge users-badge--inactive">غير نشط</span>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="users-table-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title">
                                <i class="fas fa-box"></i>
                                المنتجات ({{ $unit->products->count() }})
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>اسم المنتج</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($unit->products->take(15) as $product)
                                        <tr>
                                            <th scope="row" class="users-row-index">{{ $loop->iteration }}</th>
                                            <td>
                                                <a href="{{ route('admin.products.show', $product) }}" class="users-user-name">
                                                    {{ $product->name }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="users-empty">لا توجد منتجات بهذه الوحدة</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        AdminPremium.initDetailToggle({
            toggleId: 'unit-show-toggle',
            messages: {
                confirmActive: 'هل أنت متأكد من تفعيل هذه الوحدة؟',
                confirmInactive: 'هل أنت متأكد من إيقاف تفعيل هذه الوحدة؟',
                error: 'حدث خطأ أثناء تحديث حالة الوحدة',
            },
        });
    </script>
@stop
