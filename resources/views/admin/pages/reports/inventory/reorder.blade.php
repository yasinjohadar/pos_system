@extends('admin.layouts.master')

@section('page-title')
    تنبيهات إعادة طلب المخزون
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
                    <div>
                        <h5 class="users-page-title">تنبيهات إعادة طلب المخزون</h5>
                        <p class="users-muted-text mb-0" style="margin-top: 0.35rem;">قائمة المنتجات التي وصل رصيدها إلى حد إعادة الطلب أو أقل.</p>
                    </div>
                    <a href="{{ route('admin.reports.inventory.index') }}" class="users-btn-secondary">
                        <i class="fas fa-boxes"></i> تقرير المخزون الحالي
                    </a>
                </div>

                <div class="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 200px;">المنتج</th>
                                    <th>التصنيف</th>
                                    <th>الرصيد الحالي (إجمالي كل المخازن)</th>
                                    <th>حد إعادة الطلب</th>
                                    <th>الحد الأقصى</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.pages.reports.inventory.partials.reorder-rows')
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
