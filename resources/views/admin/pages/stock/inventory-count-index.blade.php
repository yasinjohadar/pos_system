@extends('admin.layouts.master')

@section('page-title')
    جرد المخزون
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">جرد المخزون</h5>
                    <a href="{{ route('admin.stock.balances.index') }}" class="users-btn-secondary">
                        <i class="fas fa-warehouse"></i> أرصدة المخزون
                    </a>
                </div>

                @include('admin.components.premium.flash')

                <div class="users-form-card" style="max-width: 640px;">
                    <div class="users-form-card__header">
                        <h6 class="users-form-card__title"><i class="fas fa-clipboard-list"></i> اختيار المخزن</h6>
                    </div>
                    <form method="GET" action="{{ route('admin.stock.inventory-count.index') }}" class="users-form-card__body">
                        <div class="users-form-group">
                            <label for="warehouse_id" class="users-form-label"><i class="fas fa-warehouse"></i> المخزن للجرد</label>
                            <select class="users-form-select" id="warehouse_id" name="warehouse_id" required>
                                <option value="">— اختر المخزن —</option>
                                @foreach ($warehouses as $w)
                                    <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="users-form-actions" style="border-top: none; padding-top: 0;">
                            <button type="submit" class="users-btn-submit"><i class="fas fa-list"></i> عرض نموذج الجرد</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@stop
