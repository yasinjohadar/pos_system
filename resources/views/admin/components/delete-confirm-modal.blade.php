<div class="modal fade delete-confirm-modal" id="deleteConfirmModal" tabindex="-1"
    aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content delete-confirm-modal__content">
            <div class="modal-header delete-confirm-modal__header">
                <h5 class="modal-title delete-confirm-modal__title" id="deleteConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle delete-confirm-modal__warn-icon"></i>
                    <span id="deleteConfirmModalTitle">تأكيد الحذف</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <div class="modal-body delete-confirm-modal__body">
                <div class="delete-confirm-modal__icon-wrap">
                    <i class="fas fa-trash-alt delete-confirm-modal__trash-icon"></i>
                </div>

                <p class="delete-confirm-modal__message" id="deleteConfirmModalMessage">
                    هل أنت متأكد من الحذف؟
                </p>

                <div class="delete-confirm-modal__item-box" id="deleteConfirmModalItemBox" hidden>
                    <span class="delete-confirm-modal__item-label">العنصر:</span>
                    <span class="delete-confirm-modal__item" id="deleteConfirmModalItem"></span>
                </div>

                <div class="delete-confirm-modal__details" id="deleteConfirmModalDetails" hidden></div>

                <p class="delete-confirm-modal__note">
                    <i class="fas fa-info-circle"></i>
                    لا يمكن التراجع عن هذا الإجراء بعد التنفيذ.
                </p>
            </div>

            <div class="modal-footer delete-confirm-modal__footer">
                <button type="button" class="delete-confirm-modal__btn delete-confirm-modal__btn--cancel"
                    data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    إلغاء
                </button>
                <form id="deleteConfirmModalForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-confirm-modal__btn delete-confirm-modal__btn--confirm">
                        <i class="fas fa-trash-alt"></i>
                        تأكيد الحذف
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
