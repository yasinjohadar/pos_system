/**
 * POS Terminal — barcode search, cart, checkout AJAX
 */
(function () {
    'use strict';

    var app = document.getElementById('pos-app');
    if (!app) return;

    var searchUrl = app.dataset.searchUrl;
    var checkoutUrl = app.dataset.checkoutUrl;
    var holdUrl = app.dataset.holdUrl;
    var csrf = app.dataset.csrf;

    var barcodeInput = document.getElementById('pos-barcode');
    var cartBody = document.getElementById('pos-cart-body');
    var cart = [];

    function formatMoney(n) {
        return (Math.round(n * 100) / 100).toFixed(2);
    }

    function recalcTotals() {
        var subtotal = cart.reduce(function (sum, item) {
            return sum + item.quantity * item.unit_price;
        }, 0);
        var taxRate = parseFloat(document.getElementById('pos-tax-rate').value) || 0;
        var tax = subtotal * taxRate / 100;
        var total = subtotal + tax;

        document.getElementById('pos-subtotal').textContent = formatMoney(subtotal);
        document.getElementById('pos-tax').textContent = formatMoney(tax);
        document.getElementById('pos-total').textContent = formatMoney(total);
    }

    function renderCart() {
        if (cart.length === 0) {
            cartBody.innerHTML = '<tr id="pos-cart-empty"><td colspan="5" class="users-empty">السلة فارغة — امسح باركود لإضافة منتج</td></tr>';
            recalcTotals();
            return;
        }

        cartBody.innerHTML = '';

        cart.forEach(function (item, index) {
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + escapeHtml(item.name) + '</td>' +
                '<td>' + formatMoney(item.unit_price) + '</td>' +
                '<td><input type="number" class="users-form-input pos-cart-qty" data-index="' + index + '" value="' + item.quantity + '" min="0.0001" step="any"></td>' +
                '<td>' + formatMoney(item.quantity * item.unit_price) + '</td>' +
                '<td><button type="button" class="users-action-btn users-action-btn--delete pos-remove-item" data-index="' + index + '"><i class="fa-solid fa-trash-can"></i></button></td>';
            cartBody.appendChild(tr);
        });

        recalcTotals();
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function addProduct(product) {
        var existing = cart.find(function (item) { return item.product_id === product.id; });
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({
                product_id: product.id,
                name: product.name,
                unit_price: product.base_price,
                quantity: 1,
            });
        }
        renderCart();
    }

    function searchProduct(barcode) {
        if (!barcode) return;

        fetch(searchUrl + '?barcode=' + encodeURIComponent(barcode), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.product) {
                    addProduct(data.product);
                    barcodeInput.value = '';
                    barcodeInput.focus();
                } else {
                    if (typeof AdminPremium !== 'undefined') {
                        AdminPremium.showToast('المنتج غير موجود', 'warning');
                    }
                }
            })
            .catch(function () {
                if (typeof AdminPremium !== 'undefined') {
                    AdminPremium.showToast('خطأ في البحث', 'error');
                }
            });
    }

    function getCheckoutPayload() {
        return {
            branch_id: document.getElementById('pos-branch').value,
            warehouse_id: document.getElementById('pos-warehouse').value,
            tax_rate: parseFloat(document.getElementById('pos-tax-rate').value) || 0,
            payment_method_id: document.getElementById('pos-payment-method').value,
            treasury_id: document.getElementById('pos-treasury').value,
            items: cart.map(function (item) {
                return {
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                };
            }),
        };
    }

    function checkout() {
        if (cart.length === 0) {
            if (typeof AdminPremium !== 'undefined') AdminPremium.showToast('السلة فارغة', 'warning');
            return;
        }

        var btn = document.getElementById('pos-checkout-btn');
        btn.disabled = true;

        fetch(checkoutUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(getCheckoutPayload()),
        })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    if (typeof AdminPremium !== 'undefined') {
                        AdminPremium.showToast('تم البيع — فاتورة ' + result.data.invoice_number, 'success');
                    }
                    cart = [];
                    renderCart();
                    if (result.data.print_url) {
                        window.open(result.data.print_url, '_blank');
                    }
                } else {
                    var msg = (result.data && result.data.error) || 'فشل إتمام البيع';
                    if (typeof AdminPremium !== 'undefined') AdminPremium.showToast(msg, 'error');
                }
            })
            .catch(function () {
                if (typeof AdminPremium !== 'undefined') AdminPremium.showToast('خطأ في الاتصال', 'error');
            })
            .finally(function () { btn.disabled = false; });
    }

    function holdSale() {
        if (cart.length === 0) return;

        fetch(holdUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ cart: cart }),
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (typeof AdminPremium !== 'undefined') {
                    AdminPremium.showToast('تم تعليق البيع — ' + (data.reference || ''), 'success');
                }
                cart = [];
                renderCart();
                setTimeout(function () { location.reload(); }, 800);
            })
            .catch(function () {
                if (typeof AdminPremium !== 'undefined') AdminPremium.showToast('فشل التعليق', 'error');
            });
    }

    if (barcodeInput) {
        barcodeInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchProduct(barcodeInput.value.trim());
            }
        });
    }

    if (cartBody) {
        cartBody.addEventListener('input', function (e) {
            if (e.target.classList.contains('pos-cart-qty')) {
                var idx = parseInt(e.target.dataset.index, 10);
                var qty = parseFloat(e.target.value) || 0;
                if (qty <= 0) {
                    cart.splice(idx, 1);
                } else {
                    cart[idx].quantity = qty;
                }
                renderCart();
            }
        });

        cartBody.addEventListener('click', function (e) {
            var btn = e.target.closest('.pos-remove-item');
            if (btn) {
                cart.splice(parseInt(btn.dataset.index, 10), 1);
                renderCart();
            }
        });
    }

    var taxRateEl = document.getElementById('pos-tax-rate');
    if (taxRateEl) taxRateEl.addEventListener('input', recalcTotals);

    var checkoutBtn = document.getElementById('pos-checkout-btn');
    if (checkoutBtn) checkoutBtn.addEventListener('click', checkout);

    var holdBtn = document.getElementById('pos-hold-btn');
    if (holdBtn) holdBtn.addEventListener('click', holdSale);

    var clearBtn = document.getElementById('pos-clear-btn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            cart = [];
            renderCart();
        });
    }

    document.querySelectorAll('.pos-resume-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            fetch(btn.dataset.resumeUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.cart && Array.isArray(data.cart)) {
                        cart = data.cart;
                        renderCart();
                        if (typeof AdminPremium !== 'undefined') AdminPremium.showToast('تم استئناف البيع', 'success');
                    }
                });
        });
    });

    renderCart();
})();
