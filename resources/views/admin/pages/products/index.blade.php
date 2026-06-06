@extends('admin.layouts.master')

@section('page-title')
    المنتجات
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">المنتجات</h5>
                    @can('product-create')
                        <a href="{{ route('admin.products.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة منتج
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="products-filters-form" action="{{ route('admin.products.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث (الاسم، الباركود)" value="{{ request('query') }}"
                            autocomplete="off">

                        <select name="category_id" class="users-select">
                            <option value="">جميع التصنيفات</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="products-filters-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="products-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 200px;">الاسم</th>
                                    <th style="min-width: 120px;">الباركود</th>
                                    <th style="min-width: 140px;">التصنيف</th>
                                    <th style="min-width: 80px;">الوحدة</th>
                                    <th style="min-width: 110px;">السعر الأساسي</th>
                                    <th style="min-width: 130px;">الحالة النشطة</th>
                                    <th style="min-width: 140px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="products-table-body">
                                @include('admin.pages.products.partials.table-rows')
                            </tbody>
                        </table>
                    </div>

                    <div class="users-pagination" id="products-pagination">
                        @include('admin.pages.products.partials.pagination')
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
        AdminPremium.initIndex({
            filtersFormId: 'products-filters-form',
            tableBodyId: 'products-table-body',
            paginationId: 'products-pagination',
            tableCardId: 'products-table-card',
            clearBtnId: 'products-filters-clear',
            enableCopy: true,
            toggleMessages: {
                confirmActive: 'هل أنت متأكد من تفعيل هذا المنتج؟',
                confirmInactive: 'هل أنت متأكد من إيقاف تفعيل هذا المنتج؟',
                error: 'حدث خطأ أثناء تحديث حالة المنتج',
            },
        });
    </script>
@stop
