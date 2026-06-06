(function () {
    'use strict';

    var modalEl = document.getElementById('changePasswordModal');
    if (!modalEl) {
        return;
    }

    var formEl = document.getElementById('changePasswordModalForm');
    var userBoxEl = document.getElementById('changePasswordModalUserBox');
    var userEl = document.getElementById('changePasswordModalUser');
    var errorEl = document.getElementById('changePasswordModalError');
    var passwordInput = document.getElementById('changePasswordInput');
    var confirmInput = document.getElementById('changePasswordConfirmInput');
    var generateBtn = document.getElementById('changePasswordGenerateBtn');
    var copyBtn = document.getElementById('changePasswordCopyBtn');
    var copyBtnText = document.getElementById('changePasswordCopyBtnText');
    var copyResetTimer = null;

    function hideError() {
        if (errorEl) {
            errorEl.textContent = '';
            errorEl.hidden = true;
        }
    }

    function showError(message) {
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.hidden = false;
        }
    }

    function updateCopyButtonState() {
        if (!copyBtn || !passwordInput) {
            return;
        }
        copyBtn.disabled = !passwordInput.value;
    }

    function resetCopyFeedback() {
        if (copyResetTimer) {
            clearTimeout(copyResetTimer);
            copyResetTimer = null;
        }
        if (copyBtn) {
            copyBtn.classList.remove('password-change-modal__action-btn--copied');
        }
        if (copyBtnText) {
            copyBtnText.textContent = 'نسخ';
        }
        var copyIcon = copyBtn ? copyBtn.querySelector('i') : null;
        if (copyIcon) {
            copyIcon.className = 'fas fa-copy';
        }
    }

    function resetForm() {
        if (formEl) {
            formEl.reset();
        }
        hideError();
        resetCopyFeedback();
        modalEl.querySelectorAll('.password-change-modal__input').forEach(function (input) {
            input.type = 'password';
        });
        modalEl.querySelectorAll('.password-change-modal__toggle-pw i').forEach(function (icon) {
            icon.className = 'fas fa-eye';
        });
        updateCopyButtonState();
    }

    function generatePassword(length) {
        var upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        var lower = 'abcdefghjkmnpqrstuvwxyz';
        var numbers = '23456789';
        var symbols = '!@#$%&*';
        var all = upper + lower + numbers + symbols;
        var password = '';
        var i;

        password += upper.charAt(Math.floor(Math.random() * upper.length));
        password += lower.charAt(Math.floor(Math.random() * lower.length));
        password += numbers.charAt(Math.floor(Math.random() * numbers.length));
        password += symbols.charAt(Math.floor(Math.random() * symbols.length));

        for (i = password.length; i < length; i++) {
            password += all.charAt(Math.floor(Math.random() * all.length));
        }

        return password.split('').sort(function () {
            return Math.random() - 0.5;
        }).join('');
    }

    function setGeneratedPassword(password) {
        if (!passwordInput || !confirmInput) {
            return;
        }

        passwordInput.value = password;
        confirmInput.value = password;
        passwordInput.type = 'text';
        confirmInput.type = 'text';

        var toggleIcons = modalEl.querySelectorAll('.password-change-modal__toggle-pw i');
        toggleIcons.forEach(function (icon) {
            icon.className = 'fas fa-eye-slash';
        });

        hideError();
        updateCopyButtonState();
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

    function showCopyFeedback() {
        resetCopyFeedback();

        if (copyBtn) {
            copyBtn.classList.add('password-change-modal__action-btn--copied');
        }
        if (copyBtnText) {
            copyBtnText.textContent = 'تم النسخ';
        }
        var copyIcon = copyBtn ? copyBtn.querySelector('i') : null;
        if (copyIcon) {
            copyIcon.className = 'fas fa-check';
        }

        copyResetTimer = setTimeout(function () {
            resetCopyFeedback();
        }, 2000);
    }

    modalEl.addEventListener('show.bs.modal', function (event) {
        var trigger = event.relatedTarget;
        if (!trigger) {
            return;
        }

        var action = trigger.getAttribute('data-password-action') || '';
        var userName = trigger.getAttribute('data-password-user') || '';

        resetForm();

        if (formEl && action) {
            formEl.action = action;
        }

        if (userBoxEl && userEl) {
            if (userName) {
                userEl.textContent = userName;
                userBoxEl.hidden = false;
            } else {
                userEl.textContent = '';
                userBoxEl.hidden = true;
            }
        }
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        if (formEl) {
            formEl.action = '';
        }
        resetForm();
    });

    if (generateBtn) {
        generateBtn.addEventListener('click', function () {
            setGeneratedPassword(generatePassword(12));
        });
    }

    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            if (!passwordInput || !passwordInput.value) {
                return;
            }

            copyToClipboard(passwordInput.value)
                .then(function () {
                    showCopyFeedback();
                })
                .catch(function () {
                    showError('تعذر نسخ كلمة المرور. انسخها يدوياً.');
                });
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', updateCopyButtonState);
    }

    modalEl.querySelectorAll('.password-change-modal__toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = btn.querySelector('i');
            if (!input || !icon) {
                return;
            }
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
    });

    if (formEl) {
        formEl.addEventListener('submit', function (event) {
            hideError();

            var password = passwordInput ? passwordInput.value : '';
            var confirm = confirmInput ? confirmInput.value : '';

            if (password.length < 8) {
                event.preventDefault();
                showError('كلمة المرور يجب أن تكون 8 أحرف على الأقل.');
                return;
            }

            if (password !== confirm) {
                event.preventDefault();
                showError('تأكيد كلمة المرور غير متطابق.');
            }
        });
    }
})();
