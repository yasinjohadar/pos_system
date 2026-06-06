<script>
    (function () {
        function toggleSourcePair(sourceId, treasuryWrapId, bankWrapId, treasurySelectId, bankSelectId) {
            var source = document.getElementById(sourceId);
            var treasuryWrap = document.getElementById(treasuryWrapId);
            var bankWrap = document.getElementById(bankWrapId);
            var treasurySelect = document.getElementById(treasurySelectId);
            var bankSelect = document.getElementById(bankSelectId);

            if (!source) {
                return;
            }

            function update() {
                var isTreasury = source.value === 'treasury';
                if (treasuryWrap) {
                    treasuryWrap.classList.toggle('d-none', !isTreasury);
                }
                if (bankWrap) {
                    bankWrap.classList.toggle('d-none', isTreasury);
                }
                if (treasurySelect) {
                    treasurySelect.required = isTreasury;
                }
                if (bankSelect) {
                    bankSelect.required = !isTreasury;
                }
            }

            source.addEventListener('change', update);
            update();
        }

        toggleSourcePair('from_source', 'from_treasury_wrap', 'from_bank_wrap', 'from_treasury_id', 'from_bank_account_id');
        toggleSourcePair('to_source', 'to_treasury_wrap', 'to_bank_wrap', 'to_treasury_id', 'to_bank_account_id');
    })();
</script>
