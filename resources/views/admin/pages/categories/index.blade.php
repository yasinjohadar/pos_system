@extends('admin.layouts.master')

@section('page-title')
    التصنيفات
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

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="users-header">
                    <h5 class="users-page-title">التصنيفات</h5>
                    @can('category-create')
                        <a href="{{ route('admin.categories.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة تصنيف
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="categories-filters-form" action="{{ route('admin.categories.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث بالاسم أو الرابط" value="{{ request('query') }}"
                            autocomplete="off">

                        <select name="parent_id" class="users-select">
                            <option value="">جميع التصنيفات</option>
                            <option value="null" {{ request('parent_id') === 'null' ? 'selected' : '' }}>أساسية فقط</option>
                            @foreach ($parentCategories as $c)
                                <option value="{{ $c->id }}" {{ request('parent_id') == $c->id ? 'selected' : '' }}>
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
                        <button type="button" id="categories-filters-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="categories-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 200px;">الاسم</th>
                                    <th style="min-width: 160px;">التصنيف الأب</th>
                                    <th style="min-width: 110px;">عدد المنتجات</th>
                                    <th style="min-width: 130px;">الحالة النشطة</th>
                                    <th style="min-width: 140px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="categories-table-body">
                                @include('admin.pages.categories.partials.table-rows')
                            </tbody>
                        </table>
                    </div>

                    <div class="users-pagination" id="categories-pagination">
                        @include('admin.pages.categories.partials.pagination')
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
            filtersFormId: 'categories-filters-form',
            tableBodyId: 'categories-table-body',
            paginationId: 'categories-pagination',
            tableCardId: 'categories-table-card',
            clearBtnId: 'categories-filters-clear',
            enableCopy: false,
            toggleMessages: {
                confirmActive: 'هل أنت متأكد من تفعيل هذا التصنيف؟',
                confirmInactive: 'هل أنت متأكد من إيقاف تفعيل هذا التصنيف؟',
                error: 'حدث خطأ أثناء تحديث حالة التصنيف',
            },
        });
    </script>
@stop
