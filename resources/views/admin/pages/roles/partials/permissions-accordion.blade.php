@php
    $selectedPermissions = $selectedPermissions ?? [];
@endphp

<div class="users-permissions-catalog">
    <div class="users-permissions-catalog__header">
        <div>
            <label class="users-form-label mb-1"><i class="fas fa-key"></i> الصلاحيات</label>
            <p class="users-muted-text mb-0" style="font-size: 0.8125rem;">
                إجمالي {{ $permissionsTotal }} صلاحية في {{ count($permissionGroups) }} تصنيف
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="users-btn-filter users-btn-filter--search" id="permSelectAll">
                <i class="fas fa-check-double me-1"></i> تحديد الكل
            </button>
            <button type="button" class="users-btn-filter users-btn-filter--clear" id="permDeselectAll">
                <i class="fas fa-times me-1"></i> إلغاء الكل
            </button>
        </div>
    </div>

    <div class="users-permissions-search">
        <i class="fas fa-search"></i>
        <input type="text" class="users-search-input" id="permSearch" placeholder="ابحث عن صلاحية...">
    </div>

    <div class="accordion users-permissions-accordion" id="permissionsAccordion">
        @foreach ($permissionGroups as $index => $group)
            <div class="accordion-item perm-group" data-group="{{ $group['key'] }}">
                <h2 class="accordion-header" id="heading-{{ $group['key'] }}">
                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapse-{{ $group['key'] }}"
                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-controls="collapse-{{ $group['key'] }}">
                        <i class="{{ $group['icon'] }} me-2"></i>
                        <span class="fw-bold">{{ $group['label'] }}</span>
                        <span class="users-badge users-badge--role ms-2 perm-group-count">
                            {{ $group['permissions']->count() }}
                        </span>
                    </button>
                </h2>
                <div id="collapse-{{ $group['key'] }}"
                    class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                    aria-labelledby="heading-{{ $group['key'] }}"
                    data-bs-parent="#permissionsAccordion">
                    <div class="accordion-body">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="users-btn-filter users-btn-filter--search perm-group-select-all"
                                data-group="{{ $group['key'] }}">
                                تحديد القسم
                            </button>
                            <button type="button" class="users-btn-filter users-btn-filter--clear perm-group-deselect-all"
                                data-group="{{ $group['key'] }}">
                                إلغاء القسم
                            </button>
                        </div>
                        <div class="row">
                            @foreach ($group['permissions'] as $permission)
                                <div class="col-md-4 col-lg-3 mb-2 perm-item"
                                    data-label="{{ strtolower($permission['label'] . ' ' . $permission['name']) }}">
                                    <div class="users-perm-card">
                                        <input class="users-perm-checkbox perm-checkbox" type="checkbox"
                                            name="permissions[{{ $permission['name'] }}]"
                                            value="{{ $permission['name'] }}"
                                            id="perm_{{ $permission['id'] }}"
                                            data-group="{{ $group['key'] }}"
                                            {{ in_array($permission['name'], $selectedPermissions, true) ? 'checked' : '' }}>
                                        <label class="users-perm-card__label" for="perm_{{ $permission['id'] }}">
                                            <span class="users-perm-card__title">{{ $permission['label'] }}</span>
                                            <small class="users-perm-card__slug">{{ $permission['name'] }}</small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@once
    @push('styles')
    <style>
        .users-permissions-catalog {
            margin-top: 1.5rem;
        }
        .users-permissions-catalog__header {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .users-permissions-search {
            position: relative;
            margin-bottom: 1rem;
            max-width: 420px;
        }
        .users-permissions-search i {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            z-index: 1;
        }
        .users-permissions-search .users-search-input {
            display: block;
            width: 100%;
            min-height: 42px;
            padding: 0.625rem 2.75rem 0.625rem 0.875rem;
            border: 1px solid var(--users-border, #e2e8f0);
            border-radius: 10px;
            font-size: 0.875rem;
            line-height: 1.4;
            background: var(--users-card, #fff);
            color: var(--users-text, #1e293b);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .users-permissions-search .users-search-input::placeholder {
            color: #94a3b8;
        }
        .users-permissions-search .users-search-input:focus {
            outline: none;
            border-color: var(--users-primary, #6366f1);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }
        .users-permissions-accordion .accordion-item {
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 0.75rem !important;
            overflow: hidden;
            margin-bottom: 0.75rem;
            background: #fff;
        }
        .users-permissions-accordion .accordion-button {
            background: #fff;
            box-shadow: none;
            font-size: 0.9375rem;
            padding: 1rem 1.125rem;
        }
        .users-permissions-accordion .accordion-button:not(.collapsed) {
            background: rgba(99, 102, 241, 0.06);
            color: #4338ca;
        }
        .users-permissions-accordion .accordion-button:focus {
            box-shadow: none;
            border-color: transparent;
        }
        .users-permissions-accordion .accordion-body {
            padding: 1rem 1.125rem 1.25rem;
        }
        .users-perm-card {
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            padding: 0.75rem;
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 0.625rem;
            height: 100%;
            transition: border-color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
            cursor: pointer;
        }
        .users-perm-card:has(.users-perm-checkbox:checked) {
            border-color: rgba(99, 102, 241, 0.45);
            background: rgba(99, 102, 241, 0.05);
            box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.08);
        }
        .users-perm-checkbox {
            margin-top: 0.2rem;
            flex-shrink: 0;
            accent-color: #6366f1;
        }
        .users-perm-card__label {
            cursor: pointer;
            margin: 0;
            flex: 1;
        }
        .users-perm-card__title {
            display: block;
            font-weight: 600;
            font-size: 0.875rem;
            color: #1e293b;
            line-height: 1.35;
        }
        .users-perm-card__slug {
            display: block;
            margin-top: 0.2rem;
            color: #94a3b8;
            font-size: 0.75rem;
            direction: ltr;
            text-align: right;
        }
        .perm-item.hidden-by-search,
        .perm-group.hidden-by-search {
            display: none !important;
        }
        [data-theme-mode="dark"] .users-permissions-accordion .accordion-item,
        [data-theme-mode="dark"] .users-permissions-accordion .accordion-button {
            background: rgba(255, 255, 255, 0.03);
        }
        [data-theme-mode="dark"] .users-perm-card {
            border-color: rgba(255, 255, 255, 0.1);
        }
        [data-theme-mode="dark"] .users-perm-card__title {
            color: rgba(255, 255, 255, 0.9);
        }
        [data-theme-mode="dark"] .users-perm-card:has(.users-perm-checkbox:checked) {
            background: rgba(99, 102, 241, 0.12);
        }
        [data-theme-mode="dark"] .users-permissions-search .users-search-input {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.92);
        }
        [data-theme-mode="dark"] .users-permissions-search .users-search-input::placeholder {
            color: rgba(255, 255, 255, 0.45);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var searchInput = document.getElementById('permSearch');
            var checkboxes = document.querySelectorAll('.perm-checkbox');

            function setAll(checked) {
                checkboxes.forEach(function (cb) {
                    var item = cb.closest('.perm-item');
                    if (!item || !item.classList.contains('hidden-by-search')) {
                        cb.checked = checked;
                    }
                });
            }

            document.getElementById('permSelectAll')?.addEventListener('click', function () {
                setAll(true);
            });

            document.getElementById('permDeselectAll')?.addEventListener('click', function () {
                setAll(false);
            });

            document.querySelectorAll('.perm-group-select-all').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var group = btn.getAttribute('data-group');
                    document.querySelectorAll('.perm-checkbox[data-group="' + group + '"]').forEach(function (cb) {
                        var item = cb.closest('.perm-item');
                        if (!item || !item.classList.contains('hidden-by-search')) {
                            cb.checked = true;
                        }
                    });
                });
            });

            document.querySelectorAll('.perm-group-deselect-all').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var group = btn.getAttribute('data-group');
                    document.querySelectorAll('.perm-checkbox[data-group="' + group + '"]').forEach(function (cb) {
                        cb.checked = false;
                    });
                });
            });

            searchInput?.addEventListener('input', function () {
                var query = this.value.trim().toLowerCase();

                document.querySelectorAll('.perm-item').forEach(function (item) {
                    var label = item.getAttribute('data-label') || '';
                    var match = !query || label.includes(query);
                    item.classList.toggle('hidden-by-search', !match);
                });

                document.querySelectorAll('.perm-group').forEach(function (group) {
                    var visibleItems = group.querySelectorAll('.perm-item:not(.hidden-by-search)');
                    group.classList.toggle('hidden-by-search', visibleItems.length === 0);
                });
            });

            document.querySelectorAll('.users-perm-card').forEach(function (card) {
                card.addEventListener('click', function (e) {
                    if (e.target.classList.contains('users-perm-checkbox')) {
                        return;
                    }
                    var checkbox = card.querySelector('.users-perm-checkbox');
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                    }
                });
            });
        });
    </script>
    @endpush
@endonce
