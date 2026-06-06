<script>
    (function () {
        var searchUrl = '{{ route('admin.products.search-select') }}';
        var rowIndex = document.querySelectorAll('#promotion-items-body .promotion-item-row').length;

        function initRowProductSearch(row) {
            var select = row.querySelector('.users-product-search');
            if (!select) return;
            AdminPremium.initProductSearch({
                url: searchUrl,
                selector: '#' + select.id,
            });
        }

        document.querySelectorAll('#promotion-items-body .promotion-item-row').forEach(initRowProductSearch);
        AdminPremium.initFormToggles();

        document.getElementById('promotion-add-row')?.addEventListener('click', function () {
            var tbody = document.getElementById('promotion-items-body');
            var selectId = 'promotion_product_' + rowIndex;
            var tr = document.createElement('tr');
            tr.className = 'promotion-item-row';
            tr.innerHTML =
                '<td>' +
                    '<select name="items[' + rowIndex + '][product_id]" id="' + selectId + '" class="users-form-select users-product-search" data-placeholder="ابحث بالاسم أو الباركود...">' +
                        '<option value=""></option>' +
                    '</select>' +
                '</td>' +
                '<td><input type="number" name="items[' + rowIndex + '][max_qty]" step="0.01" min="0" class="users-form-input"></td>' +
                '<td><button type="button" class="users-action-btn users-action-btn--delete promotion-remove-row" title="حذف"><i class="fas fa-trash"></i></button></td>';
            tbody.appendChild(tr);
            initRowProductSearch(tr);
            rowIndex++;
        });

        document.getElementById('promotion-items-body')?.addEventListener('click', function (e) {
            var btn = e.target.closest('.promotion-remove-row');
            if (!btn) return;
            var row = btn.closest('tr');
            if (document.querySelectorAll('#promotion-items-body .promotion-item-row').length <= 1) {
                AdminPremium.showToast('يجب الإبقاء على صف واحد على الأقل', 'error');
                return;
            }
            var select = row.querySelector('.users-product-search');
            if (select && typeof jQuery !== 'undefined' && jQuery(select).data('select2')) {
                jQuery(select).select2('destroy');
            }
            row.remove();
        });
    })();
</script>
