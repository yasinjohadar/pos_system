<script>
    (function () {
        var bankAccountSelect = document.getElementById('bank_account_id');
        var bankNameInput = document.getElementById('bank_name');

        if (!bankAccountSelect || !bankNameInput) {
            return;
        }

        function updateBankNameField() {
            var hasAccount = Boolean(bankAccountSelect.value);
            bankNameInput.disabled = hasAccount;
            if (hasAccount) {
                bankNameInput.value = '';
            }
        }

        bankAccountSelect.addEventListener('change', updateBankNameField);
        updateBankNameField();
    })();
</script>
