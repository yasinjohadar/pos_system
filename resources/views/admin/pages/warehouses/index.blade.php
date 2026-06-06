@extends('admin.layouts.master')

@section('page-title')
    المخازن
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
                    <h5 class="users-page-title">المخازن</h5>
                    @can('warehouse-create')
                        <a href="{{ route('admin.warehouses.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة مخزن
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="warehouses-filters-form" action="{{ route('admin.warehouses.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث (الاسم، الكود)" value="{{ request('query') }}"
                            autocomplete="off">

                        <select name="branch_id" class="users-select">
                            <option value="">جميع الفروع</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }}
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
                        <button type="button" id="warehouses-filters-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="warehouses-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 200px;">اسم المخزن</th>
                                    <th style="min-width: 120px;">الكود</th>
                                    <th style="min-width: 160px;">الفرع</th>
                                    <th style="min-width: 100px;">افتراضي</th>
                                    <th style="min-width: 130px;">الحالة النشطة</th>
                                    <th style="min-width: 140px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="warehouses-table-body">
                                @include('admin.pages.warehouses.partials.table-rows')
                            </tbody>
                        </table>
                    </div>

                    <div class="users-pagination" id="warehouses-pagination">
                        @include('admin.pages.warehouses.partials.pagination')
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
            filtersFormId: 'warehouses-filters-form',
            tableBodyId: 'warehouses-table-body',
            paginationId: 'warehouses-pagination',
            tableCardId: 'warehouses-table-card',
            clearBtnId: 'warehouses-filters-clear',
            enableCopy: false,
            toggleMessages: {
                confirmActive: 'هل أنت متأكد من تفعيل هذا المخزن؟',
                confirmInactive: 'هل أنت متأكد من إيقاف تفعيل هذا المخزن؟',
                error: 'حدث خطأ أثناء تحديث حالة المخزن',
            },
        });
    </script>
@stop
