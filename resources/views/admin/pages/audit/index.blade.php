@extends('admin.layouts.master')

@section('page-title')
    سجل التدقيق
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
                    <h5 class="users-page-title">سجل التدقيق</h5>
                </div>

                <div class="users-filters-card">
                    <form id="audit-filters-form" action="{{ route('admin.audit-logs.index') }}" method="GET" class="users-filters-form users-filters-form--audit">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث بالمستخدم، النموذج، المعرف، IP..."
                            value="{{ request('query') }}" autocomplete="off">

                        <select name="user_id" class="users-select">
                            <option value="">كل المستخدمين</option>
                            <option value="system" {{ request('user_id') === 'system' ? 'selected' : '' }}>النظام</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="model_type" class="users-select">
                            <option value="">كل النماذج</option>
                            @foreach ($modelTypes as $type)
                                <option value="{{ $type }}" {{ request('model_type') === $type ? 'selected' : '' }}>
                                    {{ $presenter->modelLabel($type) }}
                                </option>
                            @endforeach
                        </select>

                        <select name="action" class="users-select">
                            <option value="">كل الإجراءات</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                    {{ $presenter->actionLabel($action) }}
                                </option>
                            @endforeach
                        </select>

                        <input type="date" name="from" class="users-search-input users-filter-date"
                            value="{{ request('from') }}" title="من تاريخ">
                        <input type="date" name="to" class="users-search-input users-filter-date"
                            value="{{ request('to') }}" title="إلى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="audit-filters-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="audit-table-card">
                    <div class="table-responsive">
                        <table class="users-table users-table--audit">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>المستخدم</th>
                                    <th>العملية</th>
                                    <th>الملخص</th>
                                    <th class="text-center">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="audit-table-body">
                                @include('admin.pages.audit.partials.table-rows', [
                                    'logs' => $logs,
                                    'presenter' => $presenter,
                                ])
                            </tbody>
                        </table>
                    </div>

                    <div class="users-pagination" id="audit-pagination">
                        @include('admin.pages.audit.partials.pagination', ['logs' => $logs])
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.pages.audit.partials.detail-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        AdminPremium.initIndex({
            filtersFormId: 'audit-filters-form',
            tableBodyId: 'audit-table-body',
            paginationId: 'audit-pagination',
            tableCardId: 'audit-table-card',
            clearBtnId: 'audit-filters-clear',
            enableCopy: false,
            loadError: 'تعذّر تحميل السجلات',
            debounceMs: 400,
            onAfterFetch: function () {
                AdminPremium.closeAuditExpandRows();
                AdminPremium.initAuditLogExtras();
            },
        });
    </script>
@stop
