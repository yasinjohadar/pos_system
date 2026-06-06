@extends('admin.layouts.master')

@section('page-title')
    نقطة البيع
@stop

@section('css')
    @include('admin.components.premium.styles')
    <style>
        .pos-layout { display: grid; grid-template-columns: 1fr 380px; gap: 1.25rem; }
        @media (max-width: 992px) { .pos-layout { grid-template-columns: 1fr; } }
        .pos-barcode-input { font-size: 1.25rem; padding: 0.75rem 1rem; }
        .pos-cart-qty { width: 80px; }
        .pos-totals-row { display: flex; justify-content: space-between; padding: 0.5rem 0; }
        .pos-totals-row--total { font-size: 1.15rem; font-weight: 700; border-top: 2px solid var(--users-border); margin-top: 0.5rem; padding-top: 0.75rem; }
        .pos-shift-badge { display: inline-flex; align-items: center; gap: 0.5rem; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium" id="pos-app"
                data-search-url="{{ route('admin.pos.search-product') }}"
                data-checkout-url="{{ route('admin.pos.checkout') }}"
                data-hold-url="{{ route('admin.pos.hold') }}"
                data-csrf="{{ csrf_token() }}">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">نقطة البيع</h5>
                    <div class="users-header-actions">
                        @if ($shift)
                            <span class="pos-shift-badge users-badge users-badge--active">
                                <i class="fas fa-clock"></i>
                                وردية مفتوحة — {{ $shift->treasury->name ?? '' }}
                            </span>
                            <button type="button" class="users-btn-secondary" data-bs-toggle="modal" data-bs-target="#closeShiftModal">
                                <i class="fas fa-door-closed"></i> إغلاق الوردية
                            </button>
                        @else
                            <button type="button" class="users-btn-create" data-bs-toggle="modal" data-bs-target="#openShiftModal">
                                <i class="fas fa-door-open"></i> فتح وردية
                            </button>
                        @endif
                    </div>
                </div>

                @if (!$shift)
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-1"></i>
                        يجب فتح وردية قبل إتمام عمليات البيع.
                    </div>
                @endif

                <div class="pos-layout">
                    <div>
                        <div class="users-filters-card" style="margin-bottom: 1rem;">
                            <input type="text" id="pos-barcode" class="users-form-input pos-barcode-input" placeholder="امسح الباركود أو ابحث بالاسم..." autofocus {{ !$shift ? 'disabled' : '' }}>
                        </div>

                        <div class="users-table-card">
                            <div class="table-responsive">
                                <table class="users-table" id="pos-cart-table">
                                    <thead>
                                        <tr>
                                            <th>المنتج</th>
                                            <th>السعر</th>
                                            <th>الكمية</th>
                                            <th>الإجمالي</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="pos-cart-body">
                                        <tr id="pos-cart-empty">
                                            <td colspan="5" class="users-empty">السلة فارغة — امسح باركود لإضافة منتج</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="users-form-card">
                            <div class="users-form-card__header">
                                <h6 class="users-form-card__title"><i class="fas fa-cash-register"></i> الدفع</h6>
                            </div>
                            <div class="users-form-card__body">
                                <div class="users-form-grid">
                                    <div class="users-form-group">
                                        <label class="users-form-label">الفرع</label>
                                        <select id="pos-branch" class="users-form-select" {{ !$shift ? 'disabled' : '' }}>
                                            @foreach ($branches as $b)
                                                <option value="{{ $b->id }}" {{ $shift && $shift->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">المخزن</label>
                                        <select id="pos-warehouse" class="users-form-select" {{ !$shift ? 'disabled' : '' }}>
                                            @foreach ($warehouses as $w)
                                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">طريقة الدفع</label>
                                        <select id="pos-payment-method" class="users-form-select" {{ !$shift ? 'disabled' : '' }}>
                                            @foreach ($paymentMethods as $pm)
                                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">الخزنة</label>
                                        <select id="pos-treasury" class="users-form-select" {{ !$shift ? 'disabled' : '' }}>
                                            @foreach ($treasuries as $t)
                                                <option value="{{ $t->id }}" {{ $shift && $shift->treasury_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="users-form-group">
                                        <label class="users-form-label">نسبة الضريبة %</label>
                                        <input type="number" id="pos-tax-rate" class="users-form-input" value="0" min="0" max="100" step="0.01" {{ !$shift ? 'disabled' : '' }}>
                                    </div>
                                </div>

                                <div style="margin-top: 1rem;">
                                    <div class="pos-totals-row"><span>المجموع الفرعي</span><span id="pos-subtotal">0.00</span></div>
                                    <div class="pos-totals-row"><span>الضريبة</span><span id="pos-tax">0.00</span></div>
                                    <div class="pos-totals-row pos-totals-row--total"><span>الإجمالي</span><span id="pos-total">0.00</span></div>
                                </div>

                                <div class="users-form-actions" style="margin-top: 1.25rem; flex-direction: column;">
                                    <button type="button" id="pos-checkout-btn" class="users-btn-submit w-100" {{ !$shift ? 'disabled' : '' }}>
                                        <i class="fas fa-check"></i> إتمام البيع
                                    </button>
                                    <button type="button" id="pos-hold-btn" class="users-btn-secondary w-100" {{ !$shift ? 'disabled' : '' }}>
                                        <i class="fas fa-pause"></i> تعليق البيع
                                    </button>
                                    <button type="button" id="pos-clear-btn" class="users-btn-secondary w-100" {{ !$shift ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i> مسح السلة
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if ($heldSales->isNotEmpty())
                            <div class="users-form-card" style="margin-top: 1rem;">
                                <div class="users-form-card__header">
                                    <h6 class="users-form-card__title"><i class="fas fa-pause-circle"></i> مبيعات معلّقة</h6>
                                </div>
                                <div class="users-form-card__body">
                                    @foreach ($heldSales as $held)
                                        <button type="button" class="users-btn-secondary w-100 mb-2 pos-resume-btn"
                                            data-resume-url="{{ route('admin.pos.held.resume', $held) }}">
                                            {{ $held->reference ?? ('#' . $held->id) }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- فتح وردية --}}
    <div class="modal fade" id="openShiftModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.pos.shift.open') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">فتح وردية</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الخزنة</label>
                            <select name="treasury_id" class="form-select" required>
                                @foreach ($treasuries as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الفرع (اختياري)</label>
                            <select name="branch_id" class="form-select">
                                <option value="">—</option>
                                @foreach ($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رصيد افتتاحي</label>
                            <input type="number" name="opening_cash" class="form-control" step="0.01" min="0" value="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">فتح الوردية</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- إغلاق وردية --}}
    @if ($shift)
        <div class="modal fade" id="closeShiftModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.pos.shift.close') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">إغلاق الوردية</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">رصيد الإغلاق (نقد في الدرج)</label>
                                <input type="number" name="closing_cash" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">إغلاق الوردية</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script src="{{ asset('assets/js/pos-terminal.js') }}"></script>
@stop
