@extends('admin.layouts.master')

@section('page-title')
    تحليلات التخزين
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @if (isset($budgetAlert) && $budgetAlert)
                    <div class="email-form-alert email-form-alert--warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><strong>تنبيه:</strong> {{ $budgetAlert['message'] }}</span>
                    </div>
                @endif

                <div class="users-header">
                    <h5 class="users-page-title">تحليلات التخزين</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.storage.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-filters-card">
                    <form method="GET" action="{{ route('admin.storage.analytics') }}" class="users-filters-form users-filters-form--analytics">
                        <select name="config_id" class="users-select" required title="مكان التخزين">
                            <option value="">اختر مكان التخزين</option>
                            @foreach ($configs as $config)
                                <option value="{{ $config->id }}" {{ (string) request('config_id') === (string) $config->id ? 'selected' : '' }}>
                                    {{ $config->name }} ({{ App\Models\AppStorageConfig::DRIVERS[$config->driver] ?? $config->driver }})
                                </option>
                            @endforeach
                        </select>

                        <select name="period" class="users-select" title="الفترة">
                            <option value="day" {{ $period == 'day' ? 'selected' : '' }}>اليوم</option>
                            <option value="week" {{ $period == 'week' ? 'selected' : '' }}>هذا الأسبوع</option>
                            <option value="month" {{ $period == 'month' ? 'selected' : '' }}>هذا الشهر</option>
                            <option value="year" {{ $period == 'year' ? 'selected' : '' }}>هذه السنة</option>
                        </select>

                        <select name="file_type" class="users-select" title="نوع الملف">
                            <option value="">الكل</option>
                            <option value="image" {{ $fileType == 'image' ? 'selected' : '' }}>صور</option>
                            <option value="document" {{ $fileType == 'document' ? 'selected' : '' }}>وثائق</option>
                            <option value="video" {{ $fileType == 'video' ? 'selected' : '' }}>فيديو</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-chart-bar me-1"></i>
                            عرض الإحصائيات
                        </button>
                    </form>
                </div>

                @if ($stats && $selectedConfig)
                    <div class="storage-analytics-context mb-3">
                        <span class="users-muted-text">النتائج لـ</span>
                        <strong>{{ $selectedConfig->name }}</strong>
                    </div>

                    <div class="storage-analytics-kpi-grid">
                        <div class="users-detail-card">
                            <div class="users-detail-card__body storage-analytics-kpi">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon storage-analytics-kpi__icon--storage">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي التخزين</span>
                                        <div class="users-detail-item__value">
                                            {{ number_format($stats['total_bytes_stored'] / (1024 ** 3), 2) }} GB
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="users-detail-card">
                            <div class="users-detail-card__body storage-analytics-kpi">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon storage-analytics-kpi__icon--upload">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي الرفع</span>
                                        <div class="users-detail-item__value">
                                            {{ number_format($stats['total_bytes_uploaded'] / (1024 ** 3), 2) }} GB
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="users-detail-card">
                            <div class="users-detail-card__body storage-analytics-kpi">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon storage-analytics-kpi__icon--download">
                                        <i class="fas fa-cloud-download-alt"></i>
                                    </div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي التحميل</span>
                                        <div class="users-detail-item__value">
                                            {{ number_format($stats['total_bytes_downloaded'] / (1024 ** 3), 2) }} GB
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="users-detail-card">
                            <div class="users-detail-card__body storage-analytics-kpi">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon storage-analytics-kpi__icon--cost">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي التكلفة</span>
                                        <div class="users-detail-item__value">
                                            ${{ number_format($stats['total_cost'], 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif (request()->filled('config_id'))
                    <div class="users-table-card">
                        <div class="users-empty py-5 text-center">لا توجد بيانات للفترة المحددة.</div>
                    </div>
                @else
                    <div class="users-table-card">
                        <div class="email-empty-state py-5 text-center">
                            <i class="fas fa-chart-pie d-block mb-3"></i>
                            <p class="mb-0">اختر مكان التخزين والفترة لعرض الإحصائيات.</p>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
