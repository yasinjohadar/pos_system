@extends('admin.layouts.master')

@section('page-title')
    رسائل WhatsApp
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
                    <h5 class="users-page-title">رسائل WhatsApp</h5>
                    <a href="{{ route('admin.whatsapp-messages.create') }}" class="users-btn-create">
                        <i class="fas fa-paper-plane"></i>
                        إرسال رسالة
                    </a>
                </div>

                <div class="users-filters-card">
                    <form action="{{ route('admin.whatsapp-messages.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="search" class="users-search-input"
                            placeholder="بحث في الرسالة أو رقم المستقبل..."
                            value="{{ request('search') }}" autocomplete="off">

                        <select name="direction" class="users-select">
                            <option value="">كل الاتجاهات</option>
                            <option value="inbound" {{ request('direction') === 'inbound' ? 'selected' : '' }}>واردة</option>
                            <option value="outbound" {{ request('direction') === 'outbound' ? 'selected' : '' }}>صادرة</option>
                        </select>

                        <select name="status" class="users-select">
                            <option value="">كل الحالات</option>
                            <option value="queued" {{ request('status') === 'queued' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>مرسل</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>مستلم</option>
                            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>مقروء</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>فشل</option>
                        </select>

                        <input type="date" name="date_from" class="users-search-input users-filter-date"
                            value="{{ request('date_from') }}" title="من تاريخ">
                        <input type="date" name="date_to" class="users-search-input users-filter-date"
                            value="{{ request('date_to') }}" title="إلى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('admin.whatsapp-messages.index') }}" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </a>
                    </form>
                </div>

                <div class="users-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 90px;">الاتجاه</th>
                                    <th style="min-width: 160px;">المستقبل</th>
                                    <th style="min-width: 200px;">الرسالة</th>
                                    <th style="min-width: 110px;">الحالة</th>
                                    <th style="min-width: 130px;">التاريخ</th>
                                    <th style="min-width: 80px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('admin.pages.whatsapp-messages.partials.table-rows', ['messages' => $messages])
                            </tbody>
                        </table>
                    </div>

                    @include('admin.pages.whatsapp-messages.partials.pagination', ['messages' => $messages])
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
