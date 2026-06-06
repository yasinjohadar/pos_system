(function () {
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

    var toggle = document.getElementById('warehouse-show-toggle');
    if (toggle) {
        toggle.addEventListener('change', function () {
            var url = this.dataset.toggleUrl;
            var isActive = this.checked;
            var label = this.closest('.users-toggle').querySelector('.users-toggle-label');

            if (!url) {
                return;
            }

            var confirmMessage = isActive
                ? 'هل أنت متأكد من تفعيل هذا المخزن؟'
                : 'هل أنت متأكد من إيقاف تفعيل هذا المخزن؟';

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
                        showToast(result.data.message || 'تم تحديث حالة المخزن بنجاح', 'success');
                    } else {
                        toggle.checked = !isActive;
                        showToast(result.data.message || 'حدث خطأ أثناء تحديث حالة المخزن', 'error');
                    }
                })
                .catch(function () {
                    toggle.checked = !isActive;
                    showToast('حدث خطأ أثناء تحديث حالة المخزن', 'error');
                })
                .finally(function () {
                    toggle.disabled = false;
                    if (toggleWrap) {
                        toggleWrap.classList.remove('users-toggle--loading');
                    }
                });
        });
    }
})();
