(function (window) {
    'use strict';

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function init(config) {
        config = config || {};

        var providerSelect = document.getElementById('whatsapp_provider');
        var metaSettings = document.getElementById('meta-settings');
        var customApiSettings = document.getElementById('custom-api-settings');
        var whatsappWebSettings = document.getElementById('whatsapp-web-settings');
        var providerWebLinks = document.getElementById('provider-web-links');
        var testBtn = document.getElementById('test-connection-btn');
        var form = document.getElementById('whatsapp-settings-form');
        var inlineResult = document.getElementById('test-connection-result');
        var modalResult = document.getElementById('whatsapp-test-modal-result');
        var modalElement = document.getElementById('whatsappTestModal');
        var testModal = modalElement && window.bootstrap ? new bootstrap.Modal(modalElement) : null;

        if (!providerSelect) {
            return;
        }

        function setRequired(id, required) {
            var field = document.getElementById(id);
            if (field) {
                if (required) {
                    field.setAttribute('required', 'required');
                } else {
                    field.removeAttribute('required');
                }
            }
        }

        function toggleProviderSettings() {
            var provider = providerSelect.value;

            if (metaSettings) {
                metaSettings.hidden = provider !== 'meta';
            }
            if (customApiSettings) {
                customApiSettings.hidden = provider !== 'custom_api';
            }
            if (whatsappWebSettings) {
                whatsappWebSettings.hidden = provider !== 'whatsapp_web';
            }
            if (providerWebLinks) {
                providerWebLinks.hidden = provider !== 'whatsapp_web';
            }

            setRequired('api_version', provider === 'meta');
            setRequired('phone_number_id', provider === 'meta');
            setRequired('verify_token', provider === 'meta');
            setRequired('custom_api_url', provider === 'custom_api');
        }

        providerSelect.addEventListener('change', toggleProviderSettings);
        toggleProviderSettings();

        if (!testBtn || !form || !config.testUrl) {
            return;
        }

        var isTesting = false;

        testBtn.addEventListener('click', function () {
            if (isTesting) {
                return;
            }

            isTesting = true;
            var originalHtml = testBtn.innerHTML;
            testBtn.disabled = true;
            testBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الاختبار...';

            if (modalResult) {
                modalResult.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2 mb-0">جاري اختبار الاتصال...</p></div>';
            }
            if (testModal) {
                testModal.show();
            }
            if (inlineResult) {
                inlineResult.hidden = true;
            }

            fetch(config.testUrl, {
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
                    var html = '<div class="email-form-alert' + (success ? '' : ' email-form-alert--warning') + '">' +
                        '<i class="fas fa-' + (success ? 'check-circle' : 'times-circle') + '"></i>' +
                        '<span>' + escapeHtml(message) + '</span></div>';

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
                })
                .catch(function (error) {
                    var message = 'حدث خطأ أثناء الاختبار: ' + error.message;
                    var html = '<div class="email-form-alert email-form-alert--warning"><i class="fas fa-times-circle"></i><span>' + escapeHtml(message) + '</span></div>';
                    if (modalResult) {
                        modalResult.innerHTML = html;
                    }
                    if (window.AdminPremium && AdminPremium.showToast) {
                        AdminPremium.showToast(message, 'error');
                    }
                })
                .finally(function () {
                    isTesting = false;
                    testBtn.disabled = false;
                    testBtn.innerHTML = originalHtml;
                });
        });
    }

    window.WhatsAppSettingsForm = { init: init };
})(window);
