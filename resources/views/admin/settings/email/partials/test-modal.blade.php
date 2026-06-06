<div class="modal fade" id="email-test-modal" tabindex="-1" aria-labelledby="email-test-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="email-test-modal-label">اختبار إعدادات البريد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <div class="email-form-alert mb-3">
                    <i class="fas fa-info-circle"></i>
                    <span>سيتم إرسال بريد اختباري إلى العنوان المحدد للتأكد من صحة الإعدادات.</span>
                </div>
                <div class="users-form-group mb-0">
                    <label for="email-test-input" class="users-form-label">
                        <i class="fas fa-at"></i>
                        البريد الإلكتروني للاختبار
                    </label>
                    <input type="email" class="users-form-input" id="email-test-input" placeholder="test@example.com" dir="ltr">
                </div>
                <input type="hidden" id="email-test-setting-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="users-btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="users-btn-submit" id="email-test-send-btn">
                    <i class="fas fa-paper-plane"></i>
                    إرسال بريد اختبار
                </button>
            </div>
        </div>
    </div>
</div>
