<script>
    (function () {
        var searchUrl = '{{ route('admin.products.search-select') }}';
        var rowIndex = document.querySelectorAll('#price-list-items-body .price-list-item-row').length;

        function initRowProductSearch(row) {
            var select = row.querySelector('.users-product-search');
            if (!select) return;
            AdminPremium.initProductSearch({
                url: searchUrl,
                selector: '#' + select.id,
            });
        }

        document.querySelectorAll('#price-list-items-body .price-list-item-row').forEach(initRowProductSearch);
        AdminPremium.initFormToggles();

        document.getElementById('price-list-add-row')?.addEventListener('click', function () {
            var tbody = document.getElementById('price-list-items-body');
            var selectId = 'price_list_product_' + rowIndex;
            var tr = document.createElement('tr');
            tr.className = 'price-list-item-row';
            tr.innerHTML =
                '<td>' +
                    '<select name="items[' + rowIndex + '][product_id]" id="' + selectId + '" class="users-form-select users-product-search" data-placeholder="ابحث بالاسم أو الباركود...">' +
                        '<option value=""></option>' +
                    '</select>' +
                '</td>' +
                '<td><input type="number" name="items[' + rowIndex + '][price]" step="0.01" min="0" class="users-form-input" placeholder="0.00"></td>' +
                '<td><button type="button" class="users-action-btn users-action-btn--delete price-list-remove-row" title="حذف"><i class="fas fa-trash"></i></button></td>';
            tbody.appendChild(tr);
            initRowProductSearch(tr);
            rowIndex++;
        });

        document.getElementById('price-list-items-body')?.addEventListener('click', function (e) {
            var btn = e.target.closest('.price-list-remove-row');
            if (!btn) return;
            var row = btn.closest('tr');
            if (document.querySelectorAll('#price-list-items-body .price-list-item-row').length <= 1) {
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
