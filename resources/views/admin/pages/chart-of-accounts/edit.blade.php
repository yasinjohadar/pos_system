@extends('admin.layouts.master')

@section('page-title')
    تعديل الحساب
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تعديل: {{ $chartOfAccount->name }}</h5>
                    <a href="{{ route('admin.chart-of-accounts.index') }}" class="users-btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>

                @include('admin.components.premium.flash')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @include('admin.pages.chart-of-accounts.partials.form', [
                    'action' => route('admin.chart-of-accounts.update', $chartOfAccount),
                    'method' => 'PUT',
                    'chartOfAccount' => $chartOfAccount,
                    'parents' => $parents,
                    'submitLabel' => 'حفظ التعديلات',
                ])

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>AdminPremium.initFormToggles();</script>
@stop
