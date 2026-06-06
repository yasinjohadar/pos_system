@extends('admin.layouts.master')

@section('page-title')
    منتجات بدون مبيعات
@stop

@section('css')
    @include('admin.components.premium.styles')
    <style>@include('admin.pages.reports.product-performance.partials.styles')</style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">منتجات بدون مبيعات</h5>
                    <a href="{{ route('admin.reports.product-performance.no-sales', ['format' => 'csv']) }}"
                        class="users-btn-secondary" id="product-no-sales-export">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>

                @include('admin.pages.reports.product-performance.partials.nav', ['active' => 'no-sales'])

                <div class="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 200px;">المنتج</th>
                                    <th>التصنيف</th>
                                    <th>الباركود</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.pages.reports.product-performance.partials.no-sales-rows')
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
