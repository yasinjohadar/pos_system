@extends('admin.layouts.master')

@section('page-title')
    جدولة النسخ الاحتياطية
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
                    <h5 class="users-page-title">جدولة النسخ الاحتياطية</h5>
                    <a href="{{ route('admin.backup-schedules.create') }}" class="users-btn-create">
                        <i class="fas fa-plus"></i>
                        جدولة جديدة
                    </a>
                </div>

                <div class="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 180px;">الاسم</th>
                                    <th style="min-width: 120px;">النوع</th>
                                    <th style="min-width: 100px;">التكرار</th>
                                    <th style="min-width: 90px;">الوقت</th>
                                    <th style="min-width: 100px;">الحالة</th>
                                    <th style="min-width: 140px;">التشغيل التالي</th>
                                    <th style="min-width: 160px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.pages.backup-schedules.partials.table-rows', ['schedules' => $schedules])
                            </tbody>
                        </table>
                    </div>

                    @include('admin.pages.backup-schedules.partials.pagination', ['schedules' => $schedules])
                </div>

            </div>
        </div>
    </div>

    @include('admin.components.delete-confirm-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
