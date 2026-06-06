@extends('admin.layouts.master')

@section('page-title')
    قيد يومية جديد
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">قيد يومية يدوي</h5>
                    <a href="{{ route('admin.journal-entries.index') }}" class="users-btn-secondary">
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

                <form action="{{ route('admin.journal-entries.store') }}" method="POST" id="je-form">
                    @csrf
                    <div class="users-form-layout">
                        @include('admin.components.premium.form-aside', [
                            'icon' => 'fa-book',
                            'title' => 'قيد يدوي',
                            'text' => 'أنشئ قيداً محاسبياً يدوياً. يجب أن يتساوى مجموع المدين مع مجموع الدائن.',
                            'tips' => ['أضف سطرين على الأقل', 'يمكنك الترحيل مباشرة أو الحفظ كمسودة', 'تحقق من التوازن قبل الحفظ'],
                        ])

                        <div class="users-form-card">
                            <div class="users-form-card__header">
                                <h6 class="users-form-card__title"><i class="fas fa-book"></i> بيانات القيد</h6>
                            </div>
                            <div class="users-form-card__body">
                                <div class="users-form-grid">
                                    <div class="users-form-group">
                                        <label for="entry_date" class="users-form-label">التاريخ <span class="users-form-required">*</span></label>
                                        <input type="date" name="entry_date" id="entry_date" class="users-form-input @error('entry_date') is-invalid @enderror"
                                            value="{{ old('entry_date', date('Y-m-d')) }}" required>
                                        @error('entry_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group users-form-group--full">
                                        <label for="description" class="users-form-label">الوصف</label>
                                        <input type="text" name="description" id="description" class="users-form-input @error('description') is-invalid @enderror"
                                            value="{{ old('description') }}">
                                        @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                @error('lines')<div class="alert alert-danger">{{ $message }}</div>@enderror

                                <div class="users-table-card" style="margin-top: 1rem;">
                                    <div class="users-form-card__header" style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--users-border); display: flex; justify-content: space-between; align-items: center;">
                                        <h6 class="users-form-card__title" style="margin: 0;"><i class="fas fa-list"></i> بنود القيد</h6>
                                        <button type="button" class="users-btn-secondary" id="je-add-line" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                                            <i class="fas fa-plus"></i> إضافة سطر
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="users-table" id="je-lines-table">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 200px;">الحساب</th>
                                                    <th>مدين</th>
                                                    <th>دائن</th>
                                                    <th>الوصف</th>
                                                    <th style="width: 50px;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="je-lines-body">
                                                @php $oldLines = old('lines', [['account_id' => '', 'debit' => '', 'credit' => '', 'description' => ''], ['account_id' => '', 'debit' => '', 'credit' => '', 'description' => '']]); @endphp
                                                @foreach ($oldLines as $i => $line)
                                                    <tr class="je-line-row">
                                                        <td>
                                                            <select name="lines[{{ $i }}][account_id]" class="users-form-select" required>
                                                                <option value="">— اختر حساب —</option>
                                                                @foreach ($accounts as $acc)
                                                                    <option value="{{ $acc->id }}" {{ ($line['account_id'] ?? '') == $acc->id ? 'selected' : '' }}>
                                                                        {{ $acc->code }} — {{ $acc->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><input type="number" name="lines[{{ $i }}][debit]" class="users-form-input je-debit" step="0.01" min="0" value="{{ $line['debit'] ?? '' }}"></td>
                                                        <td><input type="number" name="lines[{{ $i }}][credit]" class="users-form-input je-credit" step="0.01" min="0" value="{{ $line['credit'] ?? '' }}"></td>
                                                        <td><input type="text" name="lines[{{ $i }}][description]" class="users-form-input" value="{{ $line['description'] ?? '' }}"></td>
                                                        <td><button type="button" class="users-action-btn users-action-btn--delete je-remove-line" title="حذف"><i class="fa-solid fa-trash-can"></i></button></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-end"><strong>الإجمالي</strong></td>
                                                    <td><strong id="je-total-debit">0.00</strong></td>
                                                    <td><strong id="je-total-credit">0.00</strong></td>
                                                    <td colspan="2"><span id="je-balance-status" class="users-badge users-badge--role">—</span></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="users-form-group users-form-group--full" style="margin-top: 1rem;">
                                    <label class="users-form-toggle">
                                        <input type="checkbox" name="post_now" value="1" class="users-form-toggle-input" {{ old('post_now') ? 'checked' : '' }}>
                                        <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                                        <span class="users-form-toggle-label">ترحيل القيد مباشرة</span>
                                    </label>
                                </div>

                                <div class="users-form-actions">
                                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> حفظ القيد</button>
                                    <a href="{{ route('admin.journal-entries.index') }}" class="users-btn-secondary">إلغاء</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <template id="je-line-template">
        <tr class="je-line-row">
            <td>
                <select name="lines[__INDEX__][account_id]" class="users-form-select" required>
                    <option value="">— اختر حساب —</option>
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="lines[__INDEX__][debit]" class="users-form-input je-debit" step="0.01" min="0"></td>
            <td><input type="number" name="lines[__INDEX__][credit]" class="users-form-input je-credit" step="0.01" min="0"></td>
            <td><input type="text" name="lines[__INDEX__][description]" class="users-form-input"></td>
            <td><button type="button" class="users-action-btn users-action-btn--delete je-remove-line" title="حذف"><i class="fa-solid fa-trash-can"></i></button></td>
        </tr>
    </template>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        (function () {
            var lineIndex = {{ count(old('lines', [['account_id' => ''], ['account_id' => '']])) }};
            var tbody = document.getElementById('je-lines-body');
            var template = document.getElementById('je-line-template');

            function recalcTotals() {
                var debit = 0, credit = 0;
                tbody.querySelectorAll('.je-debit').forEach(function (el) { debit += parseFloat(el.value) || 0; });
                tbody.querySelectorAll('.je-credit').forEach(function (el) { credit += parseFloat(el.value) || 0; });
                document.getElementById('je-total-debit').textContent = debit.toFixed(2);
                document.getElementById('je-total-credit').textContent = credit.toFixed(2);
                var status = document.getElementById('je-balance-status');
                if (Math.abs(debit - credit) < 0.01 && debit > 0) {
                    status.textContent = 'متوازن';
                    status.className = 'users-badge users-badge--active';
                } else {
                    status.textContent = 'غير متوازن';
                    status.className = 'users-badge users-badge--inactive';
                }
            }

            document.getElementById('je-add-line').addEventListener('click', function () {
                var html = template.innerHTML.replace(/__INDEX__/g, lineIndex++);
                tbody.insertAdjacentHTML('beforeend', html);
            });

            tbody.addEventListener('click', function (e) {
                if (e.target.closest('.je-remove-line')) {
                    var rows = tbody.querySelectorAll('.je-line-row');
                    if (rows.length <= 2) {
                        AdminPremium.showToast('يجب أن يحتوي القيد على سطرين على الأقل', 'warning');
                        return;
                    }
                    e.target.closest('.je-line-row').remove();
                    recalcTotals();
                }
            });

            tbody.addEventListener('input', function (e) {
                if (e.target.classList.contains('je-debit') || e.target.classList.contains('je-credit')) {
                    recalcTotals();
                }
            });

            recalcTotals();
            AdminPremium.initFormToggles();
        })();
    </script>
@stop
