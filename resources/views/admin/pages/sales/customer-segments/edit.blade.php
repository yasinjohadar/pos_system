@extends('admin.layouts.master')

@section('page-title')
    تعديل شريحة العملاء
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تعديل شريحة: {{ $customerSegment->name }}</h5>
                    <a href="{{ route('admin.customer-segments.index') }}" class="users-btn-secondary">
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

                @include('admin.pages.sales.customer-segments.partials.form', [
                    'action' => route('admin.customer-segments.update', $customerSegment),
                    'method' => 'PUT',
                    'customerSegment' => $customerSegment,
                    'submitLabel' => 'حفظ التعديلات',
                ])

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        (function () {
            AdminPremium.initFormToggles();
            var colorInput = document.getElementById('color');
            var preview = document.getElementById('color-preview');
            var valueEl = document.getElementById('color-value');
            if (colorInput) {
                colorInput.addEventListener('input', function () {
                    preview.style.background = this.value;
                    valueEl.textContent = this.value;
                });
            }
        })();
    </script>
@stop
