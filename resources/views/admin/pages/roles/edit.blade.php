@extends('admin.layouts.master')

@section('page-title')
    تعديل الدور
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تعديل الدور: {{ $role->name }}</h5>
                    <a href="{{ route('roles.index') }}" class="users-btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>

                @include('admin.components.premium.flash')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="users-form-card">
                    <div class="users-form-card__header">
                        <h6 class="users-form-card__title"><i class="fas fa-user-shield"></i> بيانات الدور</h6>
                    </div>
                    <form method="POST" action="{{ route('roles.update', $role->id) }}" class="users-form-card__body">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $role->id }}">

                        <div class="users-form-grid">
                            <div class="users-form-group users-form-group--full">
                                <label for="name" class="users-form-label">
                                    <i class="fas fa-tag"></i> اسم الدور <span class="users-form-required">*</span>
                                </label>
                                <input type="text" class="users-form-input @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $role->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @include('admin.pages.roles.partials.permissions-accordion', [
                            'selectedPermissions' => old('permissions', $selectedPermissions),
                        ])

                        <div class="users-form-actions">
                            <button type="submit" class="users-btn-submit">
                                <i class="fas fa-check"></i> حفظ التعديلات
                            </button>
                            <a href="{{ route('roles.index') }}" class="users-btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
