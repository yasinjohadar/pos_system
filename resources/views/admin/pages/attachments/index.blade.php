@extends('admin.layouts.master')

@section('page-title')
    المرفقات
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
                    <h5 class="users-page-title">المرفقات</h5>
                </div>

                <div class="users-filters-card">
                    <form id="attachments-filters" action="{{ route('admin.attachments.index') }}" method="GET" class="users-filters-form">
                        <select name="attachable_type" class="users-select">
                            <option value="">نوع السجل — الكل</option>
                            @foreach ($attachableTypes as $class => $label)
                                <option value="{{ $class }}" {{ request('attachable_type') === $class ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>

                        <select name="type" class="users-select">
                            <option value="">نوع المرفق — الكل</option>
                            @foreach ($types as $k => $v)
                                <option value="{{ $k }}" {{ request('type') === $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="attachments-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="attachments-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الملف</th>
                                    <th>نوع السجل</th>
                                    <th>المعرف</th>
                                    <th>نوع المرفق</th>
                                    <th>رفع بواسطة</th>
                                    <th>التاريخ</th>
                                    <th style="min-width: 90px;">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody id="attachments-body">
                                @include('admin.pages.attachments.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="attachments-pagination">
                        @include('admin.pages.attachments.partials.pagination')
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
            filtersFormId: 'attachments-filters',
            tableBodyId: 'attachments-body',
            paginationId: 'attachments-pagination',
            tableCardId: 'attachments-card',
            clearBtnId: 'attachments-clear',
            enableCopy: false,
        });
    </script>
@stop
