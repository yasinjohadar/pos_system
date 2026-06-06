<script>
(function () {
    'use strict';

    var toggleBtn = document.getElementById('email-password-toggle');
    var passwordInput = document.getElementById('mail_password');
    var toggleIcon = document.getElementById('email-password-toggle-icon');

    if (toggleBtn && passwordInput && toggleIcon) {
        toggleBtn.addEventListener('click', function () {
            var isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            toggleIcon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
        });
    }

    var providerSelect = document.getElementById('provider');
    if (providerSelect) {
        providerSelect.addEventListener('change', async function () {
            var provider = this.value;
            if (!provider || provider === 'custom') {
                return;
            }

            try {
                var response = await fetch('/admin/settings/email/provider/' + provider);
                var data = await response.json();
                document.getElementById('mail_host').value = data.mail_host || '';
                document.getElementById('mail_port').value = data.mail_port || 587;
                document.getElementById('mail_encryption').value = data.mail_encryption || 'tls';
            } catch (error) {
                console.error('Error loading provider preset:', error);
            }
        });
    }

    window.testEmailConnection = async function (event) {
        var mailHost = document.getElementById('mail_host').value;
        var mailPort = document.getElementById('mail_port').value;
        var mailUsername = document.getElementById('mail_username').value;
        var mailPassword = document.getElementById('mail_password').value;
        var mailEncryption = document.getElementById('mail_encryption').value;
        var mailFromAddress = document.getElementById('mail_from_address').value;
        var mailFromName = document.getElementById('mail_from_name').value;
        var isEdit = {{ ($isEdit ?? false) ? 'true' : 'false' }};
        var settingId = {{ isset($emailSetting) ? (int) $emailSetting->id : 'null' }};

        if (!mailHost || !mailPort || !mailUsername || !mailFromAddress) {
            AdminPremium.showToast('يرجى ملء جميع الحقول المطلوبة قبل الاختبار', 'error');
            return;
        }

        var testEmail = prompt('أدخل البريد الإلكتروني لإرسال بريد اختباري إليه:', mailFromAddress);
        if (!testEmail) {
            return;
        }

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(testEmail)) {
            AdminPremium.showToast('يرجى إدخال بريد إلكتروني صحيح', 'error');
            return;
        }

        var testBtn = event.currentTarget;
        var originalHtml = testBtn.innerHTML;
        testBtn.disabled = true;
        testBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الاختبار...';

        try {
            var url = '{{ route('admin.settings.email.test-temp') }}';
            var body = {
                mail_host: mailHost,
                mail_port: mailPort,
                mail_username: mailUsername,
                mail_password: mailPassword,
                mail_encryption: mailEncryption,
                mail_from_address: mailFromAddress,
                mail_from_name: mailFromName || 'Test',
                test_email: testEmail,
            };

            if (isEdit && !mailPassword && settingId) {
                url = '/admin/settings/email/' + settingId + '/test';
                body = { test_email: testEmail };
            } else if (!mailPassword) {
                AdminPremium.showToast('يرجى إدخال كلمة المرور قبل الاختبار', 'error');
                testBtn.disabled = false;
                testBtn.innerHTML = originalHtml;
                return;
            }

            var response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body),
            });

            var result = await response.json();

            if (result.success) {
                AdminPremium.showToast(result.message, 'success');
            } else {
                AdminPremium.showToast(result.message, 'error');
            }
        } catch (error) {
            AdminPremium.showToast('حدث خطأ أثناء اختبار الاتصال', 'error');
        } finally {
            testBtn.disabled = false;
            testBtn.innerHTML = originalHtml;
        }
    };
})();
</script>
