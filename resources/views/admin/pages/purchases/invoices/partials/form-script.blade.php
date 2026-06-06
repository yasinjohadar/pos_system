<script>
    AdminPremium.initSupplierSearch({
        url: '{{ route('admin.suppliers.search-select') }}',
        selector: '#supplier_id',
        minimumInputLength: 0,
    });

    AdminPremium.initInvoiceForm({
        formId: 'invoice-form',
        tbodyId: 'items-tbody',
        addRowBtnId: 'add-item-row',
        branchSelectId: 'branch_id',
        warehouseSelectId: 'warehouse_id',
        rowClass: 'invoice-item-row',
        removeBtnClass: 'invoice-remove-row',
        qtyClass: 'invoice-qty',
        priceClass: 'invoice-price',
        totalClass: 'invoice-line-total',
        productSelectClass: 'users-product-search',
        productIdPrefix: 'invoice_product_',
    });
</script>
