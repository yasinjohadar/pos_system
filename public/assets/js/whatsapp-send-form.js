(function (window) {
    'use strict';

    function init(config) {
        config = config || {};

        var sendTypeIndividual = document.getElementById('send_type_individual');
        var sendTypeBroadcast = document.getElementById('send_type_broadcast');
        var individualFields = document.getElementById('individual-fields');
        var broadcastFields = document.getElementById('broadcast-fields');
        var placeholdersInfo = document.getElementById('placeholders-info');
        var individualPlaceholdersInfo = document.getElementById('individual-placeholders-info');
        var toInput = document.getElementById('to');
        var studentSearch = document.getElementById('student_search');
        var studentsCountSpan = document.getElementById('students-count');
        var messageForm = document.getElementById('message-form');
        var typeSelect = document.getElementById('type');
        var messageField = document.getElementById('message-field');
        var templateFields = document.getElementById('template-fields');
        var messageInput = document.getElementById('message');
        var templateNameInput = document.getElementById('template_name');
        var languageInput = document.getElementById('language');

        if (!messageForm || !sendTypeIndividual || !sendTypeBroadcast) {
            return;
        }

        if (window.jQuery && studentSearch && config.searchStudentsUrl) {
            jQuery(studentSearch).select2({
                placeholder: 'ابحث عن مستخدم...',
                allowClear: true,
                dir: 'rtl',
                language: {
                    noResults: function () { return 'لا توجد نتائج'; },
                    searching: function () { return 'جاري البحث...'; },
                },
                ajax: {
                    url: config.searchStudentsUrl,
                    dataType: 'json',
                    delay: 300,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    data: function (params) {
                        return { search: params.term };
                    },
                    processResults: function (data) {
                        if (!Array.isArray(data)) {
                            return { results: [] };
                        }

                        return {
                            results: data.map(function (student) {
                                return {
                                    id: student.id,
                                    text: student.name + ' (' + (student.email || '') + ') - ' + (student.phone || ''),
                                };
                            }),
                        };
                    },
                    cache: true,
                },
                minimumInputLength: 2,
            });

            jQuery(studentSearch).on('select2:select', function (e) {
                var textParts = e.params.data.text.split(' - ');
                if (textParts.length > 1) {
                    toInput.value = textParts[textParts.length - 1].trim();
                    individualPlaceholdersInfo.hidden = false;
                    toInput.removeAttribute('required');
                }
            });

            jQuery(studentSearch).on('select2:clear', function () {
                toInput.value = '';
                individualPlaceholdersInfo.hidden = true;
                toInput.setAttribute('required', 'required');
            });
        }

        function toggleSendType() {
            var isBroadcast = sendTypeBroadcast.checked;

            individualFields.hidden = isBroadcast;
            broadcastFields.hidden = !isBroadcast;
            placeholdersInfo.hidden = !isBroadcast;

            if (isBroadcast) {
                individualPlaceholdersInfo.hidden = true;
                toInput.removeAttribute('required');
                updateStudentsCount();
            } else {
                individualPlaceholdersInfo.hidden = !(studentSearch && studentSearch.value);
                if (!studentSearch || !studentSearch.value) {
                    toInput.setAttribute('required', 'required');
                }
            }
        }

        function toggleMessageType() {
            var isTemplate = typeSelect.value === 'template';

            messageField.hidden = isTemplate;
            templateFields.hidden = !isTemplate;

            if (isTemplate) {
                messageInput.removeAttribute('required');
                templateNameInput.setAttribute('required', 'required');
                languageInput.setAttribute('required', 'required');
            } else {
                messageInput.setAttribute('required', 'required');
                templateNameInput.removeAttribute('required');
                languageInput.removeAttribute('required');
            }
        }

        function updateStudentsCount() {
            if (!studentsCountSpan || !config.studentsCountUrl) {
                return;
            }

            fetch(config.studentsCountUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    studentsCountSpan.textContent = data.count || 0;
                })
                .catch(function () {
                    studentsCountSpan.textContent = '0';
                });
        }

        sendTypeIndividual.addEventListener('change', toggleSendType);
        sendTypeBroadcast.addEventListener('change', toggleSendType);
        typeSelect.addEventListener('change', toggleMessageType);

        toggleSendType();
        toggleMessageType();

        messageForm.addEventListener('submit', function (e) {
            if (sendTypeIndividual.checked && !toInput.value && !(studentSearch && studentSearch.value)) {
                e.preventDefault();
                if (window.AdminPremium && AdminPremium.showToast) {
                    AdminPremium.showToast('يرجى إدخال رقم الهاتف أو اختيار مستخدم', 'error');
                } else {
                    alert('يرجى إدخال رقم الهاتف أو اختيار مستخدم');
                }
                return false;
            }

            if (typeSelect.value === 'text' && !messageInput.value.trim()) {
                e.preventDefault();
                if (window.AdminPremium && AdminPremium.showToast) {
                    AdminPremium.showToast('يرجى إدخال نص الرسالة', 'error');
                } else {
                    alert('يرجى إدخال نص الرسالة');
                }
                return false;
            }
        });
    }

    window.WhatsAppSendForm = { init: init };
})(window);
