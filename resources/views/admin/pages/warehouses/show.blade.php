@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المخزن
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="users-header">
                    <h5 class="users-page-title">تفاصيل المخزن: {{ $warehouse->name }}</h5>
                    <div class="users-header-actions">
                        @can('warehouse-edit')
                            <a href="{{ route('admin.warehouses.edit', $warehouse) }}" class="users-btn-edit">
                                <i class="fas fa-edit"></i>
                                تعديل المخزن
                            </a>
                        @endcan
                        <a href="{{ route('admin.warehouses.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid" style="grid-template-columns: 1fr;">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title">
                                <i class="fas fa-warehouse"></i>
                                بيانات المخزن
                            </h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-profile">
                                <div class="users-avatar">
                                    <i class="fas fa-boxes-stacked"></i>
                                </div>
                                <div>
                                    <h6 class="users-detail-profile__name">{{ $warehouse->name }}</h6>
                                    <span class="users-detail-profile__code">
                                        {{ $warehouse->code ? 'الكود: ' . $warehouse->code : 'بدون كود' }}
                                    </span>
                                </div>
                            </div>

                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-building"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الفرع</span>
                                        <div class="users-detail-item__value">
                                            @if ($warehouse->branch)
                                                @can('branch-show')
                                                    <a href="{{ route('admin.branches.show', $warehouse->branch) }}" class="users-user-name" style="cursor: pointer;">
                                                        {{ $warehouse->branch->name }}
                                                    </a>
                                                @else
                                                    {{ $warehouse->branch->name }}
                                                @endcan
                                            @else
                                                <span class="users-muted-text">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-map-marker-alt"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">العنوان</span>
                                        <div class="users-detail-item__value">
                                            {{ $warehouse->address ?? '—' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-star"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">مخزن افتراضي</span>
                                        <div class="users-detail-item__value">
                                            @if ($warehouse->is_default)
                                                <span class="users-badge users-badge--role">افتراضي للفرع</span>
                                            @else
                                                <span class="users-muted-text">لا</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-toggle-on"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة النشطة</span>
                                        <div class="users-detail-item__value">
                                            @can('warehouse-edit')
                                                <label class="users-toggle">
                                                    <input type="checkbox"
                                                        class="users-toggle-input"
                                                        id="warehouse-show-toggle"
                                                        data-toggle-url="{{ route('admin.warehouses.toggle-status', $warehouse) }}"
                                                        {{ $warehouse->is_active ? 'checked' : '' }}>
                                                    <span class="users-toggle-track">
                                                        <span class="users-toggle-thumb"></span>
                                                    </span>
                                                    <span class="users-toggle-label">
                                                        {{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </label>
                                            @else
                                                @if ($warehouse->is_active)
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
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        AdminPremium.initDetailToggle({
            toggleId: 'warehouse-show-toggle',
            messages: {
                confirmActive: 'هل أنت متأكد من تفعيل هذا المخزن؟',
                confirmInactive: 'هل أنت متأكد من إيقاف تفعيل هذا المخزن؟',
                error: 'حدث خطأ أثناء تحديث حالة المخزن',
            },
        });
        AdminPremium.initCopyButtons('.users-premium');
    </script>
@stop
