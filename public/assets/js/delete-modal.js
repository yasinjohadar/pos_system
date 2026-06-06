(function () {
    'use strict';

    var modalEl = document.getElementById('deleteConfirmModal');
    if (!modalEl) {
        return;
    }

    var titleEl = document.getElementById('deleteConfirmModalTitle');
    var messageEl = document.getElementById('deleteConfirmModalMessage');
    var itemBoxEl = document.getElementById('deleteConfirmModalItemBox');
    var itemEl = document.getElementById('deleteConfirmModalItem');
    var detailsEl = document.getElementById('deleteConfirmModalDetails');
    var formEl = document.getElementById('deleteConfirmModalForm');

    modalEl.addEventListener('show.bs.modal', function (event) {
        var trigger = event.relatedTarget;
        if (!trigger) {
            return;
        }

        var action = trigger.getAttribute('data-delete-action') || '';
        var title = trigger.getAttribute('data-delete-title') || 'تأكيد الحذف';
        var message = trigger.getAttribute('data-delete-message') || 'هل أنت متأكد من الحذف؟';
        var item = trigger.getAttribute('data-delete-item') || '';
        var details = trigger.getAttribute('data-delete-details') || '';

        if (titleEl) {
            titleEl.textContent = title;
        }

        if (messageEl) {
            messageEl.textContent = message;
        }

        if (itemBoxEl && itemEl) {
            if (item) {
                itemEl.textContent = item;
                itemBoxEl.hidden = false;
            } else {
                itemEl.textContent = '';
                itemBoxEl.hidden = true;
            }
        }

        if (detailsEl) {
            if (details) {
                detailsEl.textContent = details;
                detailsEl.hidden = false;
            } else {
                detailsEl.textContent = '';
                detailsEl.hidden = true;
            }
        }

        if (formEl && action) {
            formEl.action = action;
        }
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        if (formEl) {
            formEl.action = '';
        }
        if (detailsEl) {
            detailsEl.textContent = '';
            detailsEl.hidden = true;
        }
        if (itemBoxEl) {
            itemBoxEl.hidden = true;
        }
    });
})();
