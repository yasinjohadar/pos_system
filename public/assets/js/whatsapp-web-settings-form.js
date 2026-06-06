(function (window) {
    'use strict';

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function showResult(success, message, config) {
        var html = '<div class="email-form-alert' + (success ? '' : ' email-form-alert--warning') + '">' +
            '<i class="fas fa-' + (success ? 'check-circle' : 'times-circle') + '"></i>' +
            '<span>' + escapeHtml(message) + '</span></div>';

        var modalResult = document.getElementById('whatsapp-web-test-modal-result');
        var inlineResult = document.getElementById('test-connection-result');

        if (modalResult) {
            modalResult.innerHTML = html;
        }
        if (inlineResult) {
            inlineResult.className = 'storage-test-result email-form-alert' + (success ? '' : ' email-form-alert--warning');
            inlineResult.innerHTML = html;
            inlineResult.hidden = false;
        }
        if (window.AdminPremium && AdminPremium.showToast) {
            AdminPremium.showToast(message, success ? 'success' : 'error');
        }
    }

    function testConnection(config) {
        var form = document.getElementById('whatsapp-web-settings-form');
        if (!form || !config.testUrl) {
            return Promise.reject(new Error('Form not found'));
        }

        var modalElement = document.getElementById('whatsappWebTestModal');
        var testModal = modalElement && window.bootstrap ? bootstrap.Modal.getOrCreateInstance(modalElement) : null;
        var modalResult = document.getElementById('whatsapp-web-test-modal-result');

        if (modalResult) {
            modalResult.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 mb-0">جاري اختبار الاتصال...</p></div>';
        }
        if (testModal) {
            testModal.show();
        }

        return fetch(config.testUrl, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                var success = result.ok && result.data.success;
                var message = result.data.message || (success ? 'تم الاتصال بنجاح!' : 'فشل الاتصال');
                showResult(success, message, config);
            });
    }

    function refreshConnectionStatus(config) {
        if (!config.sessionId || !config.statusUrlTemplate) {
            window.location.reload();
            return;
        }

        var statusCard = document.getElementById('connected-status');
        if (statusCard) {
            statusCard.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 mb-0">جاري تحديث الحالة...</p></div>';
        }

        fetch(config.statusUrlTemplate.replace('__SESSION__', config.sessionId), {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
            .then(function (response) { return response.json(); })
            .then(function () {
                window.location.reload();
            })
            .catch(function () {
                window.location.reload();
            });
    }

    function disconnectSession(config) {
        if (!config.sessionId || !config.disconnectUrlTemplate) {
            return;
        }

        if (!window.confirm('هل أنت متأكد من قطع الاتصال؟')) {
            return;
        }

        fetch(config.disconnectUrlTemplate.replace('__SESSION__', config.sessionId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    window.location.reload();
                } else if (window.AdminPremium && AdminPremium.showToast) {
                    AdminPremium.showToast(data.message || 'فشل قطع الاتصال', 'error');
                } else {
                    alert(data.message || 'فشل قطع الاتصال');
                }
            })
            .catch(function () {
                if (window.AdminPremium && AdminPremium.showToast) {
                    AdminPremium.showToast('حدث خطأ أثناء قطع الاتصال', 'error');
                }
            });
    }

    function init(config) {
        config = config || {};

        var testBtn = document.getElementById('test-connection-btn');
        var refreshBtn = document.getElementById('refresh-status-btn');
        var disconnectBtn = document.getElementById('disconnect-btn');
        var isTesting = false;

        if (testBtn) {
            testBtn.addEventListener('click', function () {
                if (isTesting) {
                    return;
                }

                isTesting = true;
                var originalHtml = testBtn.innerHTML;
                testBtn.disabled = true;
                testBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الاختبار...';

                testConnection(config)
                    .catch(function (error) {
                        showResult(false, 'حدث خطأ أثناء الاختبار: ' + error.message, config);
                    })
                    .finally(function () {
                        isTesting = false;
                        testBtn.disabled = false;
                        testBtn.innerHTML = originalHtml;
                    });
            });
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function () {
                refreshConnectionStatus(config);
            });
        }

        if (disconnectBtn) {
            disconnectBtn.addEventListener('click', function () {
                disconnectSession(config);
            });
        }

        if (config.autoRefresh && config.sessionId) {
            setInterval(function () {
                refreshConnectionStatus(config);
            }, 30000);
        }
    }

    window.WhatsAppWebSettingsForm = { init: init };
})(window);
