<script>
    (function () {
        AdminPremium.initFormToggles();
        AdminPremium.initPhoneInputs();

        document.querySelectorAll('.users-no-autofill').forEach(function (input) {
            input.setAttribute('readonly', 'readonly');
            input.addEventListener('focus', function () {
                this.removeAttribute('readonly');
            });
        });

        var photoInput = document.getElementById('photo-input');
        if (photoInput) {
            photoInput.addEventListener('change', function () {
                if (!this.files || !this.files[0]) return;

                var reader = new FileReader();
                reader.onload = function (e) {
                    var wrap = document.getElementById('user-photo-preview-wrap');
                    if (!wrap) return;

                    wrap.innerHTML = '<img id="user-photo-preview" src="' + e.target.result + '" alt="معاينة">';
                };
                reader.readAsDataURL(this.files[0]);
            });
        }

        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('.users-roles-select').select2({
                placeholder: 'اختر الأدوار',
                allowClear: true,
                dir: 'rtl',
                width: '100%',
                language: {
                    noResults: function () { return 'لا توجد أدوار'; },
                },
            });
        }
    })();
</script>
