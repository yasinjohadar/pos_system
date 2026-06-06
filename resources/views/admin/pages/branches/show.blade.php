@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الفرع
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
                    <h5 class="users-page-title">تفاصيل الفرع: {{ $branch->name }}</h5>
                    <div class="users-header-actions">
                        @can('warehouse-create')
                            <a href="{{ route('admin.warehouses.create', ['branch_id' => $branch->id]) }}" class="users-btn-success">
                                <i class="fas fa-warehouse"></i>
                                إضافة مخزن
                            </a>
                        @endcan
                        @can('branch-edit')
                            <a href="{{ route('admin.branches.edit', $branch) }}" class="users-btn-edit">
                                <i class="fas fa-edit"></i>
                                تعديل الفرع
                            </a>
                        @endcan
                        <a href="{{ route('admin.branches.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title">
                                <i class="fas fa-building"></i>
                                بيانات الفرع
                            </h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-profile">
                                <div class="users-avatar">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <h6 class="users-detail-profile__name">{{ $branch->name }}</h6>
                                    <span class="users-detail-profile__code">
                                        {{ $branch->code ? 'الكود: ' . $branch->code : 'بدون كود' }}
                                    </span>
                                </div>
                            </div>

                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-phone"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الهاتف</span>
                                        <div class="users-detail-item__value">
                                            @if ($branch->phone)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $branch->phone) }}"
                                                    target="_blank" class="users-phone-cell" title="فتح WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                    <span>{{ $branch->phone }}</span>
                                                </a>
                                            @else
                                                <span class="users-muted-text">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-envelope"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">البريد</span>
                                        <div class="users-detail-item__value">
                                            @if ($branch->email)
                                                <div class="users-email-cell">
                                                    <a href="mailto:{{ $branch->email }}" class="users-email-link">
                                                        {{ $branch->email }}
                                                    </a>
                                                    <button type="button" class="users-copy-btn" data-copy="{{ $branch->email }}"
                                                        title="نسخ البريد">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
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
                                            {{ $branch->address ?? '—' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-toggle-on"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة النشطة</span>
                                        <div class="users-detail-item__value">
                                            @can('branch-edit')
                                                <label class="users-toggle">
                                                    <input type="checkbox"
                                                        class="users-toggle-input"
                                                        id="branch-show-toggle"
                                                        data-toggle-url="{{ route('admin.branches.toggle-status', $branch) }}"
                                                        {{ $branch->is_active ? 'checked' : '' }}>
                                                    <span class="users-toggle-track">
                                                        <span class="users-toggle-thumb"></span>
                                                    </span>
                                                    <span class="users-toggle-label">
                                                        {{ $branch->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </label>
                                            @else
                                                @if ($branch->is_active)
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
                                <i class="fas fa-warehouse"></i>
                                المخازن المرتبطة بالفرع
                            </h6>
                            @can('warehouse-create')
                                <a href="{{ route('admin.warehouses.create', ['branch_id' => $branch->id]) }}" class="users-btn-success" style="padding: 0.375rem 0.875rem; font-size: 0.8125rem;">
                                    <i class="fas fa-plus"></i>
                                    إضافة مخزن
                                </a>
                            @endcan
                        </div>
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>اسم المخزن</th>
                                        <th>الكود</th>
                                        <th>افتراضي</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($branch->warehouses as $warehouse)
                                        <tr>
                                            <th scope="row" class="users-row-index">{{ $loop->iteration }}</th>
                                            <td>
                                                <div class="users-user-cell">
                                                    <div class="users-avatar" style="width: 34px; height: 34px; font-size: 0.75rem;">
                                                        <i class="fas fa-boxes-stacked"></i>
                                                    </div>
                                                    <span class="users-user-name" style="cursor: default;">{{ $warehouse->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($warehouse->code)
                                                    <span class="users-badge users-badge--role">{{ $warehouse->code }}</span>
                                                @else
                                                    <span class="users-muted-text">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($warehouse->is_default)
                                                    <span class="users-badge users-badge--role">افتراضي</span>
                                                @else
                                                    <span class="users-muted-text">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($warehouse->is_active)
                                                    <span class="users-badge users-badge--active">نشط</span>
                                                @else
                                                    <span class="users-badge users-badge--inactive">غير نشط</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="users-actions">
                                                    @can('warehouse-edit')
                                                        <a class="users-action-btn users-action-btn--edit"
                                                            href="{{ route('admin.warehouses.edit', $warehouse) }}"
                                                            title="تعديل المخزن">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </a>
                                                    @endcan
                                                    @can('warehouse-delete')
                                                        <button type="button" class="users-action-btn users-action-btn--delete"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteConfirmModal"
                                                            data-delete-action="{{ route('admin.warehouses.destroy', $warehouse) }}"
                                                            data-delete-title="حذف المخزن"
                                                            data-delete-message="هل أنت متأكد من حذف هذا المخزن؟"
                                                            data-delete-item="{{ $warehouse->name }}"
                                                            title="حذف المخزن">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="users-empty">لا توجد مخازن لهذا الفرع</td>
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

    @include('admin.components.delete-confirm-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        AdminPremium.initDetailToggle({
            toggleId: 'branch-show-toggle',
            messages: {
                confirmActive: 'هل أنت متأكد من تفعيل هذا الفرع؟',
                confirmInactive: 'هل أنت متأكد من إيقاف تفعيل هذا الفرع؟',
                error: 'حدث خطأ أثناء تحديث حالة الفرع',
            },
        });
        AdminPremium.initCopyButtons('.users-premium');
    </script>
@stop
