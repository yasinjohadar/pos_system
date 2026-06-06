@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التصنيف
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تفاصيل التصنيف: {{ $category->name }}</h5>
                    <div class="users-header-actions">
                        @can('category-edit')
                            <a href="{{ route('admin.categories.edit', $category) }}" class="users-btn-edit">
                                <i class="fas fa-edit"></i>
                                تعديل التصنيف
                            </a>
                        @endcan
                        <a href="{{ route('admin.categories.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title">
                                <i class="fas fa-tags"></i>
                                بيانات التصنيف
                            </h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-profile">
                                <div class="users-avatar">
                                    @if ($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                                    @else
                                        <i class="fas fa-folder"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="users-detail-profile__name">{{ $category->name }}</h6>
                                    <span class="users-detail-profile__code">
                                        {{ $category->slug ? 'الرابط: ' . $category->slug : 'بدون رابط' }}
                                    </span>
                                </div>
                            </div>

                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-sitemap"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">التصنيف الأب</span>
                                        <div class="users-detail-item__value">
                                            @if ($category->parent)
                                                @can('category-show')
                                                    <a href="{{ route('admin.categories.show', $category->parent) }}" class="users-email-link">
                                                        {{ $category->parent->name }}
                                                    </a>
                                                @else
                                                    {{ $category->parent->name }}
                                                @endcan
                                            @else
                                                <span class="users-muted-text">تصنيف رئيسي</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-align-right"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الوصف</span>
                                        <div class="users-detail-item__value">
                                            {{ $category->description ?? '—' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-sort-numeric-down"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الترتيب</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-badge users-badge--role">{{ $category->order }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-toggle-on"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة النشطة</span>
                                        <div class="users-detail-item__value">
                                            @can('category-edit')
                                                <label class="users-toggle">
                                                    <input type="checkbox"
                                                        class="users-toggle-input"
                                                        id="category-show-toggle"
                                                        data-toggle-url="{{ route('admin.categories.toggle-status', $category) }}"
                                                        {{ $category->is_active ? 'checked' : '' }}>
                                                    <span class="users-toggle-track">
                                                        <span class="users-toggle-thumb"></span>
                                                    </span>
                                                    <span class="users-toggle-label">
                                                        {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </label>
                                            @else
                                                @if ($category->is_active)
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
                                المنتجات ({{ $category->products->count() }})
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>اسم المنتج</th>
                                        <th>السعر</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($category->products->take(15) as $product)
                                        <tr>
                                            <th scope="row" class="users-row-index">{{ $loop->iteration }}</th>
                                            <td>
                                                <a href="{{ route('admin.products.show', $product) }}" class="users-user-name">
                                                    {{ $product->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="users-badge users-badge--role">{{ number_format($product->base_price, 2) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="users-empty">لا توجد منتجات في هذا التصنيف</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($category->products->count() > 15)
                            <div class="users-pagination" style="border-top: 1px solid var(--users-border);">
                                <span class="users-muted-text" style="padding: 0.75rem 1rem; display: block;">
                                    و {{ $category->products->count() - 15 }} منتج آخر
                                </span>
                            </div>
                        @endif
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
            toggleId: 'category-show-toggle',
            messages: {
                confirmActive: 'هل أنت متأكد من تفعيل هذا التصنيف؟',
                confirmInactive: 'هل أنت متأكد من إيقاف تفعيل هذا التصنيف؟',
                error: 'حدث خطأ أثناء تحديث حالة التصنيف',
            },
        });
    </script>
@stop
