@extends('admin.layouts.master')

@section('page-title')
    قائمة المستخدمون
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @if (\Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {!! \Session::get('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (\Session::has('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {!! \Session::get('error') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="users-header">
                    <h5 class="users-page-title">كافة المستخدمين</h5>
                    <a href="{{ route('users.create') }}" class="users-btn-create">
                        <i class="fas fa-user-plus"></i>
                        إنشاء مستخدم جديد
                    </a>
                </div>

                <div class="users-filters-card">
                    <form id="users-filters-form" action="{{ route('users.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث بالاسم أو الإيميل أو الهاتف" value="{{ request('query') }}"
                            autocomplete="off">

                        <select name="is_active" class="users-select">
                            <option value="">كل الحالات النشطة</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <select name="status" class="users-select">
                            <option value="">كل الحالات</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>مفعل</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>موقوف</option>
                            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>محظور</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="users-filters-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 180px;">اسم المستخدم</th>
                                    <th style="min-width: 200px;">البريد</th>
                                    <th style="min-width: 130px;">الهاتف</th>
                                    <th style="min-width: 130px;">آخر دخول</th>
                                    <th style="min-width: 140px;">الأدوار</th>
                                    <th style="min-width: 100px;">الحالة</th>
                                    <th style="min-width: 130px;">الحالة النشطة</th>
                                    <th style="min-width: 140px;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body">
                                @include('admin.pages.users.partials.table-rows')
                            </tbody>
                        </table>
                    </div>

                    <div class="users-pagination" id="users-pagination">
                        @include('admin.pages.users.partials.pagination')
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.components.delete-confirm-modal')
    @include('admin.components.password-change-modal')
@stop

@section('script')
    <script src="{{ asset('assets/js/users-index.js') }}"></script>
@stop
