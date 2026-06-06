(function (window) {
    'use strict';

    function init(config) {
        config = config || {};

        var currentSessionId = null;
        var statusCheckInterval = null;

        function getEl(id) {
            return document.getElementById(id);
        }

        function sanitizeErrorMessage(message) {
            if (!message) {
                return 'حدث خطأ أثناء الاتصال';
            }

            if (message.indexOf('cURL error') !== -1 || message.indexOf('Couldn\'t connect') !== -1) {
                return 'لا يمكن الاتصال بخدمة Node.js. تأكد من تشغيل الخدمة على العنوان المحدد في الإعدادات.';
            }

            if (message.length > 220) {
                return message.substring(0, 220) + '…';
            }

            return message;
        }

        function showError(message) {
            var errorContainer = getEl('error-container');
            var errorMessage = getEl('error-message');
            var actionButtons = getEl('action-buttons');
            var loadingContainer = getEl('loading-container');

            if (errorContainer && errorMessage) {
                errorMessage.textContent = sanitizeErrorMessage(message);
                errorContainer.hidden = false;
            }
            if (actionButtons) {
                actionButtons.hidden = false;
            }
            if (loadingContainer) {
                loadingContainer.hidden = true;
            }
            if (window.AdminPremium && AdminPremium.showToast) {
                AdminPremium.showToast(message, 'error');
            }
        }

        function displayQrCode(qrCodeData) {
            var qrContainer = getEl('qr-container');
            var qrDisplay = getEl('qr-code-display');

            if (!qrContainer || !qrDisplay) {
                return;
            }

            if (qrCodeData.indexOf('data:image') === 0) {
                qrDisplay.innerHTML = '<img src="' + qrCodeData + '" alt="QR Code" style="max-width: 280px;">';
            } else if (qrCodeData.indexOf('<svg') === 0) {
                qrDisplay.innerHTML = qrCodeData;
            } else {
                qrDisplay.innerHTML = '<img src="data:image/png;base64,' + qrCodeData + '" alt="QR Code" style="max-width: 280px;">';
            }

            qrContainer.hidden = false;
        }

        function checkStatus(sessionId) {
            var controller = new AbortController();
            var timeoutId = setTimeout(function () {
                controller.abort();
            }, 5000);

            fetch(config.statusUrlTemplate.replace('__SESSION__', sessionId), {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                signal: controller.signal,
            })
                .then(function (response) {
                    clearTimeout(timeoutId);
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (data.success && data.connected) {
                        if (statusCheckInterval) {
                            clearInterval(statusCheckInterval);
                        }
                        window.location.reload();
                    } else if (data.success && data.status === 'connecting' && data.qr_code) {
                        displayQrCode(data.qr_code);
                    }
                })
                .catch(function () {
                    clearTimeout(timeoutId);
                });
        }

        function startStatusCheck(sessionId) {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
            }

            statusCheckInterval = setInterval(function () {
                checkStatus(sessionId);
            }, 3000);

            checkStatus(sessionId);
        }

        function startConnection() {
            var loadingContainer = getEl('loading-container');
            var qrContainer = getEl('qr-container');
            var errorContainer = getEl('error-container');
            var actionButtons = getEl('action-buttons');

            if (loadingContainer) {
                loadingContainer.hidden = false;
            }
            if (qrContainer) {
                qrContainer.hidden = true;
            }
            if (errorContainer) {
                errorContainer.hidden = true;
            }
            if (actionButtons) {
                actionButtons.hidden = true;
            }

            var controller = new AbortController();
            var timeoutId = setTimeout(function () {
                controller.abort();
            }, 10000);

            fetch(config.startUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                signal: controller.signal,
            })
                .then(function (response) {
                    clearTimeout(timeoutId);
                    if (!response.ok) {
                        return response.json().then(function (data) {
                            throw new Error(data.message || 'HTTP ' + response.status);
                        });
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (data.success) {
                        currentSessionId = data.session_id;
                        if (data.qr_code) {
                            displayQrCode(data.qr_code);
                            startStatusCheck(data.session_id);
                        } else {
                            showError('لم يتم الحصول على QR Code. تأكد من أن Node.js service يعمل.');
                        }
                    } else {
                        showError(data.message || 'فشل بدء عملية الربط');
                    }
                })
                .catch(function (error) {
                    clearTimeout(timeoutId);
                    var errorMessage = 'حدث خطأ أثناء الاتصال';

                    if (error.name === 'AbortError') {
                        errorMessage = 'انتهت مهلة الاتصال. تأكد من أن Node.js service يعمل على: ' + (config.nodejsUrl || 'http://localhost:3000');
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    showError(errorMessage);
                })
                .finally(function () {
                    if (loadingContainer) {
                        loadingContainer.hidden = true;
                    }
                });
        }

        function disconnectSession(sessionId) {
            if (!window.confirm('هل أنت متأكد من قطع الاتصال؟')) {
                return;
            }

            fetch(config.disconnectUrlTemplate.replace('__SESSION__', sessionId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
                .then(function (response) {
                    return response.json();
                })
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

        var startBtn = getEl('start-connection-btn');
        if (startBtn) {
            startBtn.addEventListener('click', startConnection);
        }

        var disconnectBtn = getEl('disconnect-session-btn');
        if (disconnectBtn) {
            disconnectBtn.addEventListener('click', function () {
                disconnectSession(disconnectBtn.getAttribute('data-session-id'));
            });
        }

        window.addEventListener('beforeunload', function () {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
            }
        });
    }

    window.WhatsAppWebConnect = { init: init };
})(window);
