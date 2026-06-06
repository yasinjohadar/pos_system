(function () {
    'use strict';

    var filtersForm = document.getElementById('categories-filters-form');
    var tableBody = document.getElementById('categories-table-body');
    var paginationEl = document.getElementById('categories-pagination');
    var tableCard = document.getElementById('categories-table-card');
    var clearBtn = document.getElementById('categories-filters-clear');
    var searchInput = filtersForm ? filtersForm.querySelector('[name="query"]') : null;
    var debounceTimer = null;
    var isLoading = false;

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

    function initToggleSwitches() {
        document.querySelectorAll('#categories-table-body .users-toggle-input').forEach(function (toggle) {
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

                var confirmMessage = isActive
                    ? 'هل أنت متأكد من تفعيل هذا التصنيف؟'
                    : 'هل أنت متأكد من إيقاف تفعيل هذا التصنيف؟';

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
                            showToast(result.data.message || 'تم تحديث حالة التصنيف بنجاح', 'success');
                        } else {
                            toggle.checked = !isActive;
                            showToast(result.data.message || 'حدث خطأ أثناء تحديث حالة التصنيف', 'error');
                        }
                    })
                    .catch(function () {
                        toggle.checked = !isActive;
                        showToast('حدث خطأ أثناء تحديث حالة التصنيف', 'error');
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

    function initRowInteractions() {
        initToggleSwitches();
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

    function fetchCategories(page) {
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
            })
            .catch(function () {
                showToast('حدث خطأ أثناء تحميل البيانات', 'error');
            })
            .finally(function () {
                setLoading(false);
            });
    }

    function initFilters() {
        if (!filtersForm) {
            return;
        }

        filtersForm.addEventListener('submit', function (event) {
            event.preventDefault();
            fetchCategories(1);
        });

        filtersForm.querySelectorAll('.users-select').forEach(function (select) {
            select.addEventListener('change', function () {
                fetchCategories(1);
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    fetchCategories(1);
                }, 400);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                filtersForm.reset();
                fetchCategories(1);
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
                fetchCategories(url.searchParams.get('page') || '1');
            });
        }
    }

    function init() {
        initRowInteractions();
        initFilters();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
