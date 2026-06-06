(function () {
    'use strict';

    document.querySelectorAll('.users-form-toggle-input').forEach(function (input) {
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
})();
