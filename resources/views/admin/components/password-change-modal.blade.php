<div class="modal fade password-change-modal" id="changePasswordModal" tabindex="-1"
    aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content password-change-modal__content">
            <div class="modal-header password-change-modal__header">
                <h5 class="modal-title password-change-modal__title" id="changePasswordModalLabel">
                    <i class="fas fa-key password-change-modal__key-icon"></i>
                    <span id="changePasswordModalTitle">تعديل كلمة المرور</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>

            <form id="changePasswordModalForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body password-change-modal__body">
                    <div class="password-change-modal__icon-wrap">
                        <i class="fas fa-lock password-change-modal__lock-icon"></i>
                    </div>

                    <div class="password-change-modal__user-box" id="changePasswordModalUserBox" hidden>
                        <span class="password-change-modal__user-label">المستخدم:</span>
                        <span class="password-change-modal__user" id="changePasswordModalUser"></span>
                    </div>

                    <p class="password-change-modal__hint">
                        <i class="fas fa-info-circle"></i>
                        يجب أن تتكون كلمة المرور من 8 أحرف على الأقل.
                    </p>

                    <div class="password-change-modal__actions">
                        <button type="button" class="password-change-modal__action-btn" id="changePasswordGenerateBtn">
                            <i class="fas fa-dice"></i>
                            توليد كلمة مرور
                        </button>
                        <button type="button" class="password-change-modal__action-btn password-change-modal__action-btn--copy"
                            id="changePasswordCopyBtn" disabled>
                            <i class="fas fa-copy"></i>
                            <span id="changePasswordCopyBtnText">نسخ</span>
                        </button>
                    </div>

                    <div class="password-change-modal__field">
                        <label for="changePasswordInput" class="password-change-modal__label">كلمة المرور الجديدة</label>
                        <div class="password-change-modal__input-wrap">
                            <input type="password" name="password" id="changePasswordInput"
                                class="password-change-modal__input" required minlength="8"
                                autocomplete="new-password" placeholder="أدخل كلمة المرور الجديدة">
                            <div class="password-change-modal__input-actions">
                                <button type="button" class="password-change-modal__icon-btn password-change-modal__toggle-pw"
                                    data-target="changePasswordInput" aria-label="إظهار كلمة المرور">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="password-change-modal__field">
                        <label for="changePasswordConfirmInput" class="password-change-modal__label">تأكيد كلمة المرور</label>
                        <div class="password-change-modal__input-wrap">
                            <input type="password" name="password_confirmation" id="changePasswordConfirmInput"
                                class="password-change-modal__input" required minlength="8"
                                autocomplete="new-password" placeholder="أعد إدخال كلمة المرور">
                            <div class="password-change-modal__input-actions">
                                <button type="button" class="password-change-modal__icon-btn password-change-modal__toggle-pw"
                                    data-target="changePasswordConfirmInput" aria-label="إظهار كلمة المرور">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <p class="password-change-modal__error" id="changePasswordModalError" hidden></p>
                </div>

                <div class="modal-footer password-change-modal__footer">
                    <button type="button" class="password-change-modal__btn password-change-modal__btn--cancel"
                        data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        إلغاء
                    </button>
                    <button type="submit" class="password-change-modal__btn password-change-modal__btn--confirm">
                        <i class="fas fa-check"></i>
                        حفظ كلمة المرور
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
