/**
 * Admin Premium — مركزي لكل صفحات لوحة التحكم
 * التعديل هنا ينعكس على القوائم، النماذج، والتفاصيل.
 */
window.AdminPremium = (function () {
    'use strict';

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function showToast(message, type) {
        var container = document.getElementById('users-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'users-toast-container';
            container.className = 'users-toast-container';
            document.body.appendChild(container);
        }

        var toast = document.createElement('div');
        toast.className = 'users-toast users-toast--' + (type === 'success' ? 'success' : 'error');
        toast.innerHTML =
            '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i>' +
            '<span>' + message + '</span>';

        container.appendChild(toast);
        requestAnimationFrame(function () {
            toast.classList.add('users-toast--visible');
        });

        setTimeout(function () {
            toast.classList.remove('users-toast--visible');
            setTimeout(function () {
                toast.remove();
                if (container.children.length === 0) {
                    container.remove();
                }
            }, 300);
        }, 3500);
    }

    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text);
        }

        return new Promise(function (resolve, reject) {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                document.body.removeChild(textarea);
                resolve();
            } catch (err) {
                document.body.removeChild(textarea);
                reject(err);
            }
        });
    }

    function initCopyButtons(scopeSelector) {
        var scope = scopeSelector ? document.querySelector(scopeSelector) : document;
        if (!scope) {
            return;
        }

        scope.querySelectorAll('.users-copy-btn').forEach(function (btn) {
            if (btn.dataset.bound === '1') {
                return;
            }
            btn.dataset.bound = '1';

            btn.addEventListener('click', function () {
                var text = btn.getAttribute('data-copy');
                if (!text) {
                    return;
                }

                copyToClipboard(text)
                    .then(function () {
                        btn.classList.add('users-copy-btn--copied');
                        var icon = btn.querySelector('i');
                        if (icon) {
                            icon.className = 'fas fa-check';
                        }
                        showToast(btn.getAttribute('data-copy-message') || 'تم النسخ بنجاح', 'success');
                        setTimeout(function () {
                            btn.classList.remove('users-copy-btn--copied');
                            if (icon) {
                                icon.className = 'fas fa-copy';
                            }
                        }, 2000);
                    })
                    .catch(function () {
                        showToast('تعذر النسخ', 'error');
                    });
            });
        });
    }

    function initToggleSwitches(scopeSelector, messages) {
        var scope = scopeSelector ? document.querySelector(scopeSelector) : document;
        if (!scope) {
            return;
        }

        var msgs = Object.assign({
            confirmActive: 'هل أنت متأكد من التفعيل؟',
            confirmInactive: 'هل أنت متأكد من إيقاف التفعيل؟',
            error: 'حدث خطأ أثناء تحديث الحالة',
        }, messages || {});

        scope.querySelectorAll('.users-toggle-input[data-toggle-url]').forEach(function (toggle) {
            if (toggle.dataset.bound === '1') {
                return;
            }
            toggle.dataset.bound = '1';

            toggle.addEventListener('change', function () {
                var url = this.dataset.toggleUrl;
                var isActive = this.checked;
                var label = this.closest('.users-toggle').querySelector('.users-toggle-label');

                if (!url) {
                    return;
                }

                var confirmMessage = isActive ? msgs.confirmActive : msgs.confirmInactive;

                if (!confirm(confirmMessage)) {
                    this.checked = !isActive;
                    return;
                }

                this.disabled = true;
                var toggleWrap = this.closest('.users-toggle');
                if (toggleWrap) {
                    toggleWrap.classList.add('users-toggle--loading');
                }

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ is_active: isActive }),
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        if (result.data.success) {
                            toggle.checked = Boolean(result.data.is_active);
                            if (label) {
                                label.textContent = result.data.is_active ? 'نشط' : 'غير نشط';
                            }
                            showToast(result.data.message || msgs.success || 'تم تحديث الحالة بنجاح', 'success');
                        } else {
                            toggle.checked = !isActive;
                            showToast(result.data.message || msgs.error, 'error');
                        }
                    })
                    .catch(function () {
                        toggle.checked = !isActive;
                        showToast(msgs.error, 'error');
                    })
                    .finally(function () {
                        toggle.disabled = false;
                        if (toggleWrap) {
                            toggleWrap.classList.remove('users-toggle--loading');
                        }
                    });
            });
        });
    }

    function initFormToggles() {
        document.querySelectorAll('.users-form-toggle-input').forEach(function (input) {
            if (input.dataset.bound === '1') {
                return;
            }
            input.dataset.bound = '1';

            input.addEventListener('change', function () {
                var label = this.closest('.users-toggle').querySelector('.users-toggle-label');
                if (!label) {
                    return;
                }

                label.textContent = this.checked
                    ? (this.dataset.labelOn || 'مفعّل')
                    : (this.dataset.labelOff || 'معطّل');
            });
        });
    }

    function initIndex(config) {
        if (!config) {
            return;
        }

        var filtersForm = document.getElementById(config.filtersFormId);
        var tableBody = document.getElementById(config.tableBodyId);
        var paginationEl = document.getElementById(config.paginationId);
        var tableCard = config.tableCardId ? document.getElementById(config.tableCardId) : null;
        var clearBtn = config.clearBtnId ? document.getElementById(config.clearBtnId) : null;
        var searchInput = filtersForm ? filtersForm.querySelector('[name="query"]') : null;
        var debounceTimer = null;
        var isLoading = false;
        var scopeSelector = config.tableBodyId ? '#' + config.tableBodyId : null;

        function initRowInteractions() {
            if (config.enableCopy !== false) {
                initCopyButtons(scopeSelector);
            }
            initToggleSwitches(scopeSelector, config.toggleMessages);
        }

        function getFilterParams(page) {
            var params = new URLSearchParams(new FormData(filtersForm));
            params.delete('page');
            if (page) {
                params.set('page', page);
            }
            return params;
        }

        function updateBrowserUrl(params) {
            var url = new URL(window.location.href);
            url.search = params.toString();
            window.history.replaceState({}, '', url);
        }

        function setLoading(state) {
            isLoading = state;
            if (tableCard) {
                tableCard.classList.toggle('users-table-card--loading', state);
            }
        }

        function fetchData(page) {
            if (!filtersForm || !tableBody || !paginationEl || isLoading) {
                return;
            }

            var params = getFilterParams(page);
            var url = filtersForm.action + '?' + params.toString();

            setLoading(true);
            updateBrowserUrl(params);

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Network error');
                    }
                    return response.json();
                })
                .then(function (data) {
                    tableBody.innerHTML = data.tbody;
                    paginationEl.innerHTML = data.pagination;
                    initRowInteractions();
                    if (typeof config.onAfterFetch === 'function') {
                        config.onAfterFetch();
                    }
                })
                .catch(function () {
                    showToast(config.loadError || 'حدث خطأ أثناء تحميل البيانات', 'error');
                })
                .finally(function () {
                    setLoading(false);
                });
        }

        if (filtersForm) {
            filtersForm.addEventListener('submit', function (event) {
                event.preventDefault();
                fetchData(1);
            });

            filtersForm.querySelectorAll('.users-select').forEach(function (select) {
                select.addEventListener('change', function () {
                    fetchData(1);
                });
            });

            filtersForm.querySelectorAll('.users-filter-date, .users-filter-checkbox').forEach(function (input) {
                input.addEventListener('change', function () {
                    fetchData(1);
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        fetchData(1);
                    }, config.debounceMs || 400);
                });
            }
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (filtersForm) {
                    filtersForm.reset();
                }
                fetchData(1);
            });
        }

        if (paginationEl) {
            paginationEl.addEventListener('click', function (event) {
                var link = event.target.closest('a');
                if (!link || !paginationEl.contains(link)) {
                    return;
                }
                event.preventDefault();
                var url = new URL(link.href);
                fetchData(url.searchParams.get('page') || '1');
            });
        }

        initRowInteractions();
        if (typeof config.onAfterFetch === 'function') {
            config.onAfterFetch();
        }
    }

    function closeAuditExpandRows() {
        document.querySelectorAll('.audit-expand-row:not([hidden])').forEach(function (row) {
            row.hidden = true;
        });
        document.querySelectorAll('.audit-action-btn--expand.is-open').forEach(function (btn) {
            btn.classList.remove('is-open');
            var icon = btn.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-chevron-down';
            }
        });
    }

    function escapeAuditHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderAuditDetailModal(data) {
        var body = document.getElementById('audit-detail-modal-body');
        var entityLink = document.getElementById('audit-detail-entity-link');
        if (!body) {
            return;
        }

        var changesHtml = '';
        if (data.changes && data.changes.length) {
            changesHtml = '<table class="audit-diff-table"><thead><tr><th>الحقل</th><th>قبل</th><th>بعد</th></tr></thead><tbody>';
            data.changes.forEach(function (change) {
                changesHtml += '<tr><td>' + escapeAuditHtml(change.label) + '</td><td>' + escapeAuditHtml(change.old) + '</td><td>' + escapeAuditHtml(change.new) + '</td></tr>';
            });
            changesHtml += '</tbody></table>';
        } else {
            changesHtml = '<p class="audit-detail-empty mb-0">لا توجد تفاصيل إضافية.</p>';
        }

        body.innerHTML =
            '<div class="audit-detail-header">' +
                '<span class="audit-action-badge ' + escapeAuditHtml(data.action_badge_class) + '">' + escapeAuditHtml(data.action_label) + '</span>' +
                '<span class="audit-model-label">' + escapeAuditHtml(data.model_label) + ' #' + escapeAuditHtml(String(data.model_id)) + '</span>' +
            '</div>' +
            '<p class="audit-detail-summary">' + escapeAuditHtml(data.summary) + '</p>' +
            '<div class="audit-detail-meta">' +
                '<div><strong>المستخدم:</strong> ' + escapeAuditHtml(data.actor) + '</div>' +
                '<div><strong>التاريخ:</strong> ' + escapeAuditHtml(data.created_at || '—') + '</div>' +
                '<div><strong>IP:</strong> ' + escapeAuditHtml(data.ip_address || '—') + '</div>' +
                '<div><strong>المتصفح:</strong> <span class="audit-user-agent">' + escapeAuditHtml(data.user_agent || '—') + '</span></div>' +
            '</div>' +
            '<h6 class="audit-expand-title mt-3"><i class="fas fa-list"></i> التغييرات</h6>' +
            changesHtml;

        if (entityLink) {
            if (data.entity_url) {
                entityLink.href = data.entity_url;
                entityLink.classList.remove('d-none');
            } else {
                entityLink.classList.add('d-none');
            }
        }
    }

    function initAuditLogExtras(root) {
        var scope = root || document;

        scope.querySelectorAll('.audit-action-btn--expand').forEach(function (btn) {
            if (btn.dataset.bound === '1') {
                return;
            }
            btn.dataset.bound = '1';

            btn.addEventListener('click', function () {
                var targetId = btn.getAttribute('data-expand-target');
                var row = targetId ? document.getElementById(targetId) : null;
                if (!row) {
                    return;
                }

                var isOpen = !row.hidden;
                closeAuditExpandRows();

                if (!isOpen) {
                    row.hidden = false;
                    btn.classList.add('is-open');
                    var icon = btn.querySelector('i');
                    if (icon) {
                        icon.className = 'fas fa-chevron-up';
                    }
                }
            });
        });

        scope.querySelectorAll('.audit-action-btn--detail').forEach(function (btn) {
            if (btn.dataset.bound === '1') {
                return;
            }
            btn.dataset.bound = '1';

            btn.addEventListener('click', function () {
                var url = btn.getAttribute('data-detail-url');
                var modalEl = document.getElementById('audit-detail-modal');
                if (!url || !modalEl || typeof bootstrap === 'undefined') {
                    return;
                }

                var body = document.getElementById('audit-detail-modal-body');
                if (body) {
                    body.innerHTML = '<div class="audit-detail-loading text-center py-4"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</div>';
                }

                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();

                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Not found');
                        }
                        return response.json();
                    })
                    .then(renderAuditDetailModal)
                    .catch(function () {
                        if (body) {
                            body.innerHTML = '<p class="text-danger mb-0">تعذّر تحميل التفاصيل.</p>';
                        }
                    });
            });
        });
    }

    function initDetailToggle(config) {
        if (!config || !config.toggleId) {
            return;
        }

        var toggle = document.getElementById(config.toggleId);
        if (!toggle || !toggle.dataset.toggleUrl) {
            return;
        }

        var scope = toggle.closest('.users-premium') || document;
        initToggleSwitches(scope, config.messages);
    }

    function initProductSearch(config) {
        if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
            return;
        }

        config = config || {};
        var url = config.url;
        if (!url) {
            return;
        }

        var selector = config.selector || '.users-product-search';
        var minLen = config.minimumInputLength != null ? config.minimumInputLength : 1;

        jQuery(selector).each(function () {
            var $el = jQuery(this);
            if ($el.data('select2')) {
                return;
            }

            $el.select2({
                placeholder: $el.data('placeholder') || config.placeholder || 'ابحث بالاسم أو الباركود...',
                allowClear: true,
                dir: 'rtl',
                width: '100%',
                language: {
                    noResults: function () {
                        return 'لا توجد نتائج';
                    },
                    searching: function () {
                        return 'جاري البحث...';
                    },
                    inputTooShort: function (args) {
                        return 'أدخل ' + (args.minimum - args.input.length) + ' أحرف على الأقل';
                    },
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 300,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    data: function (params) {
                        return {
                            search: params.term,
                            q: params.term,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results || [],
                        };
                    },
                    cache: true,
                },
                minimumInputLength: minLen,
            });
        });
    }

    function initInvoiceForm(config) {
        if (!config || !config.formId) {
            return;
        }

        var form = document.getElementById(config.formId);
        if (!form) {
            return;
        }

        var tbody = document.getElementById(config.tbodyId || 'items-tbody');
        var addBtn = document.getElementById(config.addRowBtnId || 'add-item-row');
        var branchSelect = document.getElementById(config.branchSelectId || 'branch_id');
        var warehouseSelect = document.getElementById(config.warehouseSelectId || 'warehouse_id');
        var customerSelect = document.getElementById(config.customerSelectId || 'customer_id');
        var productPriceUrl = form.dataset.productPriceUrl || config.productPriceUrl;
        var productSearchUrl = form.dataset.productSearchUrl || config.productSearchUrl;
        var rowClass = config.rowClass || 'invoice-item-row';
        var removeBtnClass = config.removeBtnClass || 'invoice-remove-row';
        var qtyClass = config.qtyClass || 'invoice-qty';
        var priceClass = config.priceClass || 'invoice-price';
        var totalClass = config.totalClass || 'invoice-line-total';
        var productSelectClass = config.productSelectClass || 'users-product-search';
        var productIdPrefix = config.productIdPrefix || 'invoice_product_';
        var rowIndex = tbody ? tbody.querySelectorAll('.' + rowClass).length : 0;

        function getBranchId() {
            return branchSelect ? branchSelect.value : '';
        }

        function getCustomerId() {
            if (!customerSelect) {
                return '';
            }
            if (typeof jQuery !== 'undefined' && jQuery(customerSelect).data('select2')) {
                return jQuery(customerSelect).val() || '';
            }
            return customerSelect.value || '';
        }

        function updateRowTotal(row) {
            var qtyInput = row.querySelector('.' + qtyClass);
            var priceInput = row.querySelector('.' + priceClass);
            var totalInput = row.querySelector('.' + totalClass);
            if (!qtyInput || !priceInput || !totalInput) {
                return;
            }
            var qty = parseFloat(qtyInput.value) || 0;
            var price = parseFloat(priceInput.value) || 0;
            totalInput.value = (qty * price).toFixed(2);
        }

        function fetchProductPrice(productId, row) {
            if (!productPriceUrl || !productId || !row) {
                return;
            }

            var priceInput = row.querySelector('.' + priceClass);
            if (!priceInput) {
                return;
            }

            var params = new URLSearchParams({
                product_id: productId,
                branch_id: getBranchId(),
            });
            var customerId = getCustomerId();
            if (customerId) {
                params.set('customer_id', customerId);
            }

            fetch(productPriceUrl + '?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            })
                .then(function (r) {
                    return r.json();
                })
                .then(function (data) {
                    priceInput.value = data.price != null ? data.price : 0;
                    updateRowTotal(row);
                })
                .catch(function () {});
        }

        function initRowProductSearch(row) {
            var select = row.querySelector('.' + productSelectClass);
            if (!select || !productSearchUrl) {
                return;
            }

            AdminPremium.initProductSearch({
                url: productSearchUrl,
                selector: '#' + select.id,
            });

            if (typeof jQuery !== 'undefined') {
                jQuery(select).off('select2:select.invoice select2:clear.invoice');
                jQuery(select).on('select2:select.invoice', function (e) {
                    fetchProductPrice(e.params.data.id, row);
                });
                jQuery(select).on('select2:clear.invoice', function () {
                    var priceInput = row.querySelector('.' + priceClass);
                    if (priceInput) {
                        priceInput.value = 0;
                    }
                    updateRowTotal(row);
                });
            }
        }

        function bindRow(row) {
            var qtyInput = row.querySelector('.' + qtyClass);
            var priceInput = row.querySelector('.' + priceClass);
            var removeBtn = row.querySelector('.' + removeBtnClass);

            if (qtyInput) {
                qtyInput.addEventListener('input', function () {
                    updateRowTotal(row);
                });
            }
            if (priceInput) {
                priceInput.addEventListener('input', function () {
                    updateRowTotal(row);
                });
            }
            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    if (!tbody || tbody.querySelectorAll('.' + rowClass).length <= 1) {
                        showToast('يجب الإبقاء على بند واحد على الأقل', 'error');
                        return;
                    }
                    var select = row.querySelector('.' + productSelectClass);
                    if (select && typeof jQuery !== 'undefined' && jQuery(select).data('select2')) {
                        jQuery(select).select2('destroy');
                    }
                    row.remove();
                });
            }

            initRowProductSearch(row);
            updateRowTotal(row);
        }

        function filterWarehouses() {
            if (!warehouseSelect || !branchSelect) {
                return;
            }

            var branchId = branchSelect.value;
            var current = warehouseSelect.value;
            var hasVisible = false;

            Array.prototype.forEach.call(warehouseSelect.options, function (opt, idx) {
                if (idx === 0) {
                    opt.hidden = false;
                    return;
                }
                var match = !branchId || String(opt.dataset.branch) === String(branchId);
                opt.hidden = !match;
                if (match) {
                    hasVisible = true;
                }
            });

            if (current) {
                var selected = warehouseSelect.querySelector('option[value="' + current + '"]');
                if (selected && selected.hidden) {
                    warehouseSelect.value = '';
                }
            }

            if (!warehouseSelect.value && hasVisible) {
                var first = Array.prototype.find.call(warehouseSelect.options, function (opt, idx) {
                    return idx > 0 && !opt.hidden;
                });
                if (first) {
                    warehouseSelect.value = first.value;
                }
            }
        }

        if (tbody) {
            tbody.querySelectorAll('.' + rowClass).forEach(bindRow);
        }

        if (addBtn && tbody) {
            addBtn.addEventListener('click', function () {
                var selectId = productIdPrefix + rowIndex;
                var tr = document.createElement('tr');
                tr.className = rowClass;
                tr.innerHTML =
                    '<td>' +
                        '<select name="items[' + rowIndex + '][product_id]" id="' + selectId + '" class="users-form-select ' + productSelectClass + '" data-placeholder="ابحث بالاسم أو الباركود..." required>' +
                            '<option value=""></option>' +
                        '</select>' +
                        '<input type="hidden" name="items[' + rowIndex + '][warehouse_id]" value="">' +
                    '</td>' +
                    '<td><input type="number" step="0.001" name="items[' + rowIndex + '][quantity]" class="users-form-input ' + qtyClass + '" value="1" min="0.001"></td>' +
                    '<td><input type="number" step="0.01" name="items[' + rowIndex + '][unit_price]" class="users-form-input ' + priceClass + '" value="0" min="0"></td>' +
                    '<td><input type="text" class="users-form-input ' + totalClass + '" readonly value="0.00"></td>' +
                    '<td><button type="button" class="users-action-btn users-action-btn--delete ' + removeBtnClass + '" title="حذف"><i class="fas fa-trash"></i></button></td>';
                tbody.appendChild(tr);
                bindRow(tr);
                rowIndex++;
            });
        }

        if (branchSelect) {
            branchSelect.addEventListener('change', function () {
                filterWarehouses();
                if (tbody) {
                    tbody.querySelectorAll('.' + rowClass).forEach(function (row) {
                        var select = row.querySelector('.' + productSelectClass);
                        if (select && select.value) {
                            fetchProductPrice(select.value, row);
                        }
                    });
                }
            });
            filterWarehouses();
        }

        if (customerSelect && typeof jQuery !== 'undefined') {
            jQuery(customerSelect).on('change.invoicePrice', function () {
                if (!tbody) {
                    return;
                }
                tbody.querySelectorAll('.' + rowClass).forEach(function (row) {
                    var select = row.querySelector('.' + productSelectClass);
                    if (select && select.value) {
                        fetchProductPrice(select.value, row);
                    }
                });
            });
        }
    }

    function initCustomerSearch(config) {
        if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
            return;
        }

        config = config || {};
        var url = config.url;
        if (!url) {
            return;
        }

        var selector = config.selector || '.users-customer-search';
        var minLen = config.minimumInputLength != null ? config.minimumInputLength : 1;
        var onSelect = config.onSelect;

        jQuery(selector).each(function () {
            var $el = jQuery(this);
            if ($el.data('select2')) {
                return;
            }

            $el.select2({
                placeholder: $el.data('placeholder') || config.placeholder || 'ابحث بالاسم أو الهاتف...',
                allowClear: true,
                dir: 'rtl',
                width: '100%',
                language: {
                    noResults: function () {
                        return 'لا يوجد عملاء';
                    },
                    searching: function () {
                        return 'جاري البحث...';
                    },
                    inputTooShort: function (args) {
                        return 'أدخل ' + (args.minimum - args.input.length) + ' أحرف على الأقل';
                    },
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 300,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    data: function (params) {
                        return {
                            search: params.term,
                            q: params.term,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: (data.results || []).map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                    loyalty_points: item.loyalty_points,
                                };
                            }),
                        };
                    },
                    cache: true,
                },
                minimumInputLength: minLen,
                templateResult: function (item) {
                    if (!item.id) {
                        return item.text;
                    }
                    var pts = item.loyalty_points != null ? item.loyalty_points : 0;
                    return jQuery('<span>' + item.text + ' <small style="color:#6b7280;">— ' + pts + ' نقطة</small></span>');
                },
            });

            if (typeof onSelect === 'function') {
                $el.on('select2:select', function (e) {
                    onSelect(e.params.data, $el);
                });
                $el.on('select2:clear', function () {
                    onSelect(null, $el);
                });
            }
        });
    }

    function initSupplierSearch(config) {
        if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
            return;
        }

        config = config || {};
        var url = config.url;
        if (!url) {
            return;
        }

        var selector = config.selector || '.users-supplier-search';
        var minLen = config.minimumInputLength != null ? config.minimumInputLength : 1;
        var onSelect = config.onSelect;

        jQuery(selector).each(function () {
            var $el = jQuery(this);
            if ($el.data('select2')) {
                return;
            }

            $el.select2({
                placeholder: $el.data('placeholder') || config.placeholder || 'ابحث بالاسم أو الهاتف...',
                allowClear: true,
                dir: 'rtl',
                width: '100%',
                language: {
                    noResults: function () {
                        return 'لا يوجد موردون';
                    },
                    searching: function () {
                        return 'جاري البحث...';
                    },
                    inputTooShort: function (args) {
                        return 'أدخل ' + (args.minimum - args.input.length) + ' أحرف على الأقل';
                    },
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 300,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    data: function (params) {
                        return {
                            search: params.term,
                            q: params.term,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: (data.results || []).map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.text,
                                };
                            }),
                        };
                    },
                    cache: true,
                },
                minimumInputLength: minLen,
            });

            if (typeof onSelect === 'function') {
                $el.on('select2:select', function (e) {
                    onSelect(e.params.data, $el);
                });
                $el.on('select2:clear', function () {
                    onSelect(null, $el);
                });
            }
        });
    }

    function buildE164(countryCode, local) {
        var code = String(countryCode || '').replace(/\D/g, '');
        var digits = String(local || '').replace(/\D/g, '').replace(/^0+/, '');

        if (!code || !digits) {
            return '';
        }

        return '+' + code + digits;
    }

    function closeCountryMenu(picker) {
        var menu = picker.querySelector('.users-phone-country__menu');
        var toggle = picker.querySelector('.users-phone-country__toggle');

        if (menu) {
            menu.hidden = true;
        }

        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
        }

        picker.classList.remove('is-open');
    }

    function openCountryMenu(picker) {
        document.querySelectorAll('.users-phone-country.is-open').forEach(function (other) {
            if (other !== picker) {
                closeCountryMenu(other);
            }
        });

        var menu = picker.querySelector('.users-phone-country__menu');
        var toggle = picker.querySelector('.users-phone-country__toggle');

        if (menu) {
            menu.hidden = false;
        }

        if (toggle) {
            toggle.setAttribute('aria-expanded', 'true');
        }

        picker.classList.add('is-open');
    }

    function selectCountryOption(picker, option) {
        var hidden = picker.querySelector('.users-phone-input__country');
        var toggle = picker.querySelector('.users-phone-country__toggle');
        var flagImg = toggle ? toggle.querySelector('.users-phone-country__flag') : null;
        var dial = toggle ? toggle.querySelector('.users-phone-country__dial') : null;

        if (!hidden || !option) {
            return;
        }

        hidden.value = option.getAttribute('data-code') || '';

        if (flagImg) {
            flagImg.src = option.getAttribute('data-flag') || flagImg.src;
        }

        if (dial) {
            dial.textContent = '+' + hidden.value;
        }

        picker.querySelectorAll('.users-phone-country__option').forEach(function (item) {
            var selected = item === option;
            item.classList.toggle('is-selected', selected);
            item.setAttribute('aria-selected', selected ? 'true' : 'false');
        });

        closeCountryMenu(picker);

        var wrap = picker.closest('.users-phone-input');
        if (wrap) {
            syncPhoneInput(wrap);
        }
    }

    function initCountryPicker(picker) {
        if (!picker || picker.dataset.countryInit === '1') {
            return;
        }

        picker.dataset.countryInit = '1';

        var toggle = picker.querySelector('.users-phone-country__toggle');
        var menu = picker.querySelector('.users-phone-country__menu');

        if (toggle) {
            toggle.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                if (picker.classList.contains('is-open')) {
                    closeCountryMenu(picker);
                } else {
                    openCountryMenu(picker);
                }
            });
        }

        if (menu) {
            menu.querySelectorAll('.users-phone-country__option').forEach(function (option) {
                option.addEventListener('click', function () {
                    selectCountryOption(picker, option);
                });
            });
        }
    }

    function syncPhoneInput(wrap) {
        var field = wrap.getAttribute('data-phone-field') || 'phone';
        var country = wrap.querySelector('.users-phone-input__country');
        var local = wrap.querySelector('.users-phone-input__local');
        var hidden = wrap.querySelector('input[type="hidden"][name="' + field + '"]');

        if (!country || !local || !hidden) {
            return;
        }

        hidden.value = buildE164(country.value, local.value);
    }

    function initPhoneInputs(root) {
        var scope = root || document;
        scope.querySelectorAll('.users-phone-input').forEach(function (wrap) {
            if (wrap.dataset.phoneInit === '1') {
                return;
            }

            wrap.dataset.phoneInit = '1';

            var local = wrap.querySelector('.users-phone-input__local');
            var country = wrap.querySelector('.users-phone-input__country');
            var picker = wrap.querySelector('.users-phone-country');

            if (picker) {
                initCountryPicker(picker);
            }

            syncPhoneInput(wrap);

            if (country && country.tagName === 'SELECT') {
                country.addEventListener('change', function () {
                    syncPhoneInput(wrap);
                });
            }

            if (local) {
                local.addEventListener('input', function () {
                    this.value = this.value.replace(/\D/g, '').replace(/^0+/, '');
                    syncPhoneInput(wrap);
                });

                local.addEventListener('blur', function () {
                    this.value = this.value.replace(/\D/g, '').replace(/^0+/, '');
                    syncPhoneInput(wrap);
                });
            }

            var form = wrap.closest('form');
            if (form && !form.dataset.phoneSubmitBound) {
                form.dataset.phoneSubmitBound = '1';
                form.addEventListener('submit', function () {
                    form.querySelectorAll('.users-phone-input').forEach(syncPhoneInput);
                });
            }
        });
    }

    return {
        showToast: showToast,
        copyToClipboard: copyToClipboard,
        initCopyButtons: initCopyButtons,
        initToggleSwitches: initToggleSwitches,
        initFormToggles: initFormToggles,
        initIndex: initIndex,
        initDetailToggle: initDetailToggle,
        initProductSearch: initProductSearch,
        initCustomerSearch: initCustomerSearch,
        initSupplierSearch: initSupplierSearch,
        initInvoiceForm: initInvoiceForm,
        initPhoneInputs: initPhoneInputs,
        initAuditLogExtras: initAuditLogExtras,
        closeAuditExpandRows: closeAuditExpandRows,
    };

    document.addEventListener('DOMContentLoaded', function () {
        if (document.querySelector('.users-form-toggle-input')) {
            initFormToggles();
        }

        if (document.querySelector('.users-phone-input')) {
            initPhoneInputs();
        }

        document.addEventListener('click', function (event) {
            if (event.target.closest('.users-phone-country')) {
                return;
            }

            document.querySelectorAll('.users-phone-country.is-open').forEach(closeCountryMenu);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.users-phone-country.is-open').forEach(closeCountryMenu);
            }
        });
    });
})();
