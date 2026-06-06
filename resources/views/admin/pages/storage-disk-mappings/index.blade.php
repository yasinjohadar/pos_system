@extends('admin.layouts.master')

@section('page-title')
    ربط الأقراص
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
                    <h5 class="users-page-title">ربط الأقراص</h5>
                    <a href="{{ route('admin.storage-disk-mappings.create') }}" class="users-btn-create">
                        <i class="fas fa-plus"></i>
                        إضافة ربط
                    </a>
                </div>

                <div class="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 120px;">Disk Name</th>
                                    <th style="min-width: 180px;">التسمية</th>
                                    <th style="min-width: 160px;">التخزين الأساسي</th>
                                    <th style="min-width: 100px;">الحالة</th>
                                    <th style="min-width: 120px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.pages.storage-disk-mappings.partials.table-rows', ['mappings' => $mappings])
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.components.delete-confirm-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
