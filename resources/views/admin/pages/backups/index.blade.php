@extends('admin.layouts.master')

@section('page-title')
    النسخ الاحتياطية
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
                    <h5 class="users-page-title">النسخ الاحتياطية</h5>
                    <a href="{{ route('admin.backups.create') }}" class="users-btn-create">
                        <i class="fas fa-plus"></i>
                        نسخة احتياطية جديدة
                    </a>
                </div>

                <div class="storage-analytics-kpi-grid backups-kpi-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__body storage-analytics-kpi">
                            <div class="users-detail-item">
                                <div class="users-detail-item__icon storage-analytics-kpi__icon--storage">
                                    <i class="fas fa-copy"></i>
                                </div>
                                <div class="users-detail-item__content">
                                    <span class="users-detail-item__label">إجمالي النسخ</span>
                                    <div class="users-detail-item__value">{{ $stats['total'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="users-detail-card">
                        <div class="users-detail-card__body storage-analytics-kpi">
                            <div class="users-detail-item">
                                <div class="users-detail-item__icon storage-analytics-kpi__icon--upload">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="users-detail-item__content">
                                    <span class="users-detail-item__label">مكتملة</span>
                                    <div class="users-detail-item__value">{{ $stats['completed'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="users-detail-card">
                        <div class="users-detail-card__body storage-analytics-kpi">
                            <div class="users-detail-item">
                                <div class="users-detail-item__icon storage-analytics-kpi__icon--cost">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div class="users-detail-item__content">
                                    <span class="users-detail-item__label">فاشلة</span>
                                    <div class="users-detail-item__value">{{ $stats['failed'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="users-detail-card">
                        <div class="users-detail-card__body storage-analytics-kpi">
                            <div class="users-detail-item">
                                <div class="users-detail-item__icon storage-analytics-kpi__icon--download">
                                    <i class="fas fa-hdd"></i>
                                </div>
                                <div class="users-detail-item__content">
                                    <span class="users-detail-item__label">الحجم الإجمالي</span>
                                    <div class="users-detail-item__value">
                                        {{ number_format(($stats['total_size'] ?? 0) / 1024 / 1024, 2) }} MB
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 180px;">الاسم</th>
                                    <th style="min-width: 120px;">النوع</th>
                                    <th style="min-width: 110px;">الحالة</th>
                                    <th style="min-width: 100px;">الحجم</th>
                                    <th style="min-width: 130px;">التاريخ</th>
                                    <th style="min-width: 140px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.pages.backups.partials.table-rows', ['backups' => $backups])
                            </tbody>
                        </table>
                    </div>

                    @include('admin.pages.backups.partials.pagination', ['backups' => $backups])
                </div>

            </div>
        </div>
    </div>

    @include('admin.components.delete-confirm-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
