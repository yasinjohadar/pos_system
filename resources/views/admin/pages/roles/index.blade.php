@extends('admin.layouts.master')

@section('page-title')
    الأدوار
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
                    <h5 class="users-page-title">جدول الأدوار</h5>
                    <a href="{{ route('roles.create') }}" class="users-btn-create">
                        <i class="fas fa-plus"></i>
                        إضافة دور جديد
                    </a>
                </div>

                <div class="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th style="min-width: 220px;">اسم الدور</th>
                                    <th style="min-width: 140px;">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <th scope="row" class="users-row-index">{{ $loop->iteration }}</th>
                                        <td>
                                            <div class="users-user-cell">
                                                <div class="users-avatar">
                                                    <i class="fas fa-user-shield"></i>
                                                </div>
                                                <span class="users-badge users-badge--role">{{ $role->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="users-actions">
                                                <a class="users-action-btn users-action-btn--edit"
                                                    href="{{ route('roles.edit', $role->id) }}"
                                                    title="تعديل الدور">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <button type="button" class="users-action-btn users-action-btn--delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteConfirmModal"
                                                    data-delete-action="{{ route('roles.destroy', $role->id) }}"
                                                    data-delete-title="حذف الدور"
                                                    data-delete-message="هل أنت متأكد من حذف هذا الدور؟"
                                                    data-delete-item="{{ $role->name }}"
                                                    title="حذف الدور">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="users-empty">لا توجد بيانات متاحة</td>
                                    </tr>
                                @endforelse
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
