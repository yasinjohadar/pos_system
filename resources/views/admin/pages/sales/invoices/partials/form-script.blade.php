<script>
    AdminPremium.initCustomerSearch({
        url: '{{ route('admin.customers.search-select') }}',
        selector: '#customer_id',
        minimumInputLength: 0,
    });

    AdminPremium.initInvoiceForm({
        formId: 'invoice-form',
        tbodyId: 'items-tbody',
        addRowBtnId: 'add-item-row',
        branchSelectId: 'branch_id',
        warehouseSelectId: 'warehouse_id',
        customerSelectId: 'customer_id',
        rowClass: 'invoice-item-row',
        removeBtnClass: 'invoice-remove-row',
        qtyClass: 'invoice-qty',
        priceClass: 'invoice-price',
        totalClass: 'invoice-line-total',
        productSelectClass: 'users-product-search',
        productIdPrefix: 'invoice_product_',
    });
</script>
