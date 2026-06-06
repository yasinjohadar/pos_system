(function (window) {
    'use strict';

    function init() {
        var frequencySelect = document.getElementById('frequency');
        var daysOfWeekField = document.getElementById('days_of_week_field');
        var dayOfMonthField = document.getElementById('day_of_month_field');

        if (!frequencySelect || !daysOfWeekField || !dayOfMonthField) {
            return;
        }

        function toggleFields() {
            var frequency = frequencySelect.value;

            daysOfWeekField.hidden = frequency !== 'weekly';
            dayOfMonthField.hidden = frequency !== 'monthly';
        }

        frequencySelect.addEventListener('change', toggleFields);
        toggleFields();
    }

    window.BackupScheduleForm = { init: init };
})(window);
