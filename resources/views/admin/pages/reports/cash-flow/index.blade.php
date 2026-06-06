@extends('admin.layouts.master')

@section('page-title')
    التدفقات النقدية
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
                    <h5 class="users-page-title">التدفقات النقدية</h5>
                </div>

                <div class="users-filters-card">
                    <form action="{{ route('admin.reports.cash-flow.index') }}" method="GET" class="users-filters-form">
                        <input type="date" name="from" class="users-search-input users-filter-date" value="{{ $from }}" title="من تاريخ">
                        <input type="date" name="to" class="users-search-input users-filter-date" value="{{ $to }}" title="إلى تاريخ">
                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                    </form>
                </div>

                <div class="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>البند</th>
                                    <th>المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>التدفقات التشغيلية</td>
                                    <td><span class="users-amount {{ $operating >= 0 ? 'users-qty--in' : 'users-qty--out' }}">{{ number_format($operating, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td>التدفقات الاستثمارية</td>
                                    <td><span class="users-amount">{{ number_format($investing, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td>التدفقات التمويلية</td>
                                    <td><span class="users-amount">{{ number_format($financing, 2) }}</span></td>
                                </tr>
                                <tr style="font-weight: bold; border-top: 2px solid var(--users-border);">
                                    <td>صافي التغير في النقد</td>
                                    <td><span class="users-amount {{ $netChange >= 0 ? 'users-qty--in' : 'users-qty--out' }}">{{ number_format($netChange, 2) }}</span></td>
                                </tr>
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
