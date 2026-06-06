@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المنتج
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تفاصيل المنتج: {{ $product->name }}</h5>
                    <div class="users-header-actions">
                        @can('product-edit')
                            <a href="{{ route('admin.products.edit', $product) }}" class="users-btn-edit">
                                <i class="fas fa-edit"></i>
                                تعديل المنتج
                            </a>
                        @endcan
                        <a href="{{ route('admin.products.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title">
                                <i class="fas fa-box"></i>
                                بيانات المنتج
                            </h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-profile">
                                <div class="users-avatar">
                                    @if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image))
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <i class="fas fa-box"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="users-detail-profile__name">{{ $product->name }}</h6>
                                    @if ($product->barcode)
                                        <div class="users-email-cell">
                                            <span class="users-detail-profile__code" dir="ltr">الباركود: {{ $product->barcode }}</span>
                                            <button type="button" class="users-copy-btn" data-copy="{{ $product->barcode }}"
                                                data-copy-message="تم نسخ الباركود"
                                                title="نسخ الباركود" aria-label="نسخ الباركود">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="users-detail-profile__code">بدون باركود</span>
                                    @endif
                                </div>
                            </div>

                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-tags"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">التصنيف</span>
                                        <div class="users-detail-item__value">
                                            @if ($product->category)
                                                <a href="{{ route('admin.categories.show', $product->category) }}" class="users-email-link">
                                                    {{ $product->category->name }}
                                                </a>
                                            @else
                                                <span class="users-muted-text">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-ruler-combined"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الوحدة</span>
                                        <div class="users-detail-item__value">
                                            {{ $product->unit->name ?? '—' }}
                                            @if ($product->unit?->symbol)
                                                ({{ $product->unit->symbol }})
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-tag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">السعر الأساسي</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-badge users-badge--role">{{ number_format($product->base_price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-coins"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">سعر التكلفة</span>
                                        <div class="users-detail-item__value">
                                            {{ $product->cost_price !== null ? number_format($product->cost_price, 2) : '—' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-bell"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">حد تنبيه المخزون</span>
                                        <div class="users-detail-item__value">{{ $product->min_stock_alert }}</div>
                                    </div>
                                </div>

                                @if ($product->description)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-align-right"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">الوصف</span>
                                            <div class="users-detail-item__value">{{ $product->description }}</div>
                                        </div>
                                    </div>
                                @endif

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-toggle-on"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة النشطة</span>
                                        <div class="users-detail-item__value">
                                            @can('product-edit')
                                                <label class="users-toggle">
                                                    <input type="checkbox"
                                                        class="users-toggle-input"
                                                        id="product-show-toggle"
                                                        data-toggle-url="{{ route('admin.products.toggle-status', $product) }}"
                                                        {{ $product->is_active ? 'checked' : '' }}>
                                                    <span class="users-toggle-track">
                                                        <span class="users-toggle-thumb"></span>
                                                    </span>
                                                    <span class="users-toggle-label">
                                                        {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </label>
                                            @else
                                                @if ($product->is_active)
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
                                <i class="fas fa-money-bill-wave"></i>
                                أسعار إضافية
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th>الفرع</th>
                                        <th>نوع السعر</th>
                                        <th>القيمة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($product->prices as $p)
                                        <tr>
                                            <td>{{ $p->branch_id ? $p->branch->name : 'افتراضي (جميع الفروع)' }}</td>
                                            <td>{{ \App\Models\ProductPrice::PRICE_TYPES[$p->price_type] ?? $p->price_type }}</td>
                                            <td>
                                                <span class="users-badge users-badge--role">{{ number_format($p->value, 2) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="users-empty">لا توجد أسعار إضافية — يُستخدم السعر الأساسي</td>
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
        AdminPremium.initCopyButtons('.users-premium');

        AdminPremium.initDetailToggle({
            toggleId: 'product-show-toggle',
            messages: {
                confirmActive: 'هل أنت متأكد من تفعيل هذا المنتج؟',
                confirmInactive: 'هل أنت متأكد من إيقاف تفعيل هذا المنتج؟',
                error: 'حدث خطأ أثناء تحديث حالة المنتج',
            },
        });
    </script>
@stop
