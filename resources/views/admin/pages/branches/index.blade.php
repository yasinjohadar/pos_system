@extends('admin.layouts.master')

@section('page-title')
    الفروع
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
                    <h5 class="users-page-title">الفروع</h5>
                    @can('branch-create')
                        <a href="{{ route('admin.branches.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة فرع
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="branches-filters-form" action="{{ route('admin.branches.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث (الاسم، الكود، الهاتف، البريد)" value="{{ request('query') }}"
                            autocomplete="off">

                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="branches-filters-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="branches-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 180px;">الاسم</th>
                                    <th style="min-width: 100px;">الكود</th>
                                    <th style="min-width: 200px;">البريد</th>
                                    <th style="min-width: 130px;">الهاتف</th>
                                    <th style="min-width: 110px;">عدد المخازن</th>
                                    <th style="min-width: 130px;">الحالة النشطة</th>
                                    <th style="min-width: 140px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="branches-table-body">
                                @include('admin.pages.branches.partials.table-rows')
                            </tbody>
                        </table>
                    </div>

                    <div class="users-pagination" id="branches-pagination">
                        @include('admin.pages.branches.partials.pagination')
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
            filtersFormId: 'branches-filters-form',
            tableBodyId: 'branches-table-body',
            paginationId: 'branches-pagination',
            tableCardId: 'branches-table-card',
            clearBtnId: 'branches-filters-clear',
            toggleMessages: {
                confirmActive: 'هل أنت متأكد من تفعيل هذا الفرع؟',
                confirmInactive: 'هل أنت متأكد من إيقاف تفعيل هذا الفرع؟',
                error: 'حدث خطأ أثناء تحديث حالة الفرع',
            },
        });
    </script>
@stop
