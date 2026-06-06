(function (window) {
    'use strict';

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function premiumizeConfigFields(root) {
        root.querySelectorAll('.mb-3').forEach(function (wrap) {
            wrap.classList.remove('mb-3');
            wrap.classList.add('users-form-group', 'users-form-group--full');
        });
        root.querySelectorAll('.form-label').forEach(function (label) {
            label.classList.remove('form-label');
            label.classList.add('users-form-label');
        });
        root.querySelectorAll('.form-control').forEach(function (input) {
            input.classList.remove('form-control');
            input.classList.add('users-form-input');
        });
        root.querySelectorAll('.form-select').forEach(function (select) {
            select.classList.remove('form-select');
            select.classList.add('users-form-select');
        });
    }

    function infoMessage(text) {
        return '<div class="email-form-alert"><i class="fas fa-info-circle"></i><span>' + text + '</span></div>';
    }

    function warningMessage(text) {
        return '<div class="email-form-alert email-form-alert--warning"><i class="fas fa-exclamation-triangle"></i><span>' + text + '</span></div>';
    }

    function buildCreateTemplates() {
        return {
            local: '<div class="mb-3"><label class="form-label">المسار (اختياري)</label><input type="text" class="form-control" name="config[path]" value="public"></div>',
            s3: '<div class="mb-3"><label class="form-label">Access Key ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_key_id]" required></div>' +
                '<div class="mb-3"><label class="form-label">Secret Access Key <span class="text-danger">*</span></label><input type="password" class="form-control" name="config[secret_access_key]" required></div>' +
                '<div class="mb-3"><label class="form-label">Bucket <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[bucket]" required></div>' +
                '<div class="mb-3"><label class="form-label">Region</label><input type="text" class="form-control" name="config[region]" value="us-east-1"></div>' +
                '<div class="mb-3"><label class="form-label">Endpoint (لـ S3-compatible)</label><input type="text" class="form-control" name="config[endpoint]" placeholder="https://s3.region.amazonaws.com"></div>' +
                '<div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="config[use_path_style]" value="1" id="use_path_style"><label class="form-check-label" for="use_path_style">Use Path Style Endpoint</label></div></div>',
            digitalocean: '<div class="mb-3"><label class="form-label">Access Key ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_key_id]" required></div>' +
                '<div class="mb-3"><label class="form-label">Secret Access Key <span class="text-danger">*</span></label><input type="password" class="form-control" name="config[secret_access_key]" required></div>' +
                '<div class="mb-3"><label class="form-label">Bucket <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[bucket]" required></div>' +
                '<div class="mb-3"><label class="form-label">Region</label><select class="form-select" name="config[region]"><option value="nyc3">NYC3</option><option value="nyc1">NYC1</option><option value="sfo3">SFO3</option><option value="sgp1">SGP1</option><option value="sfo2">SFO2</option><option value="ams3">AMS3</option></select></div>',
            wasabi: '<div class="mb-3"><label class="form-label">Access Key ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_key_id]" required></div>' +
                '<div class="mb-3"><label class="form-label">Secret Access Key <span class="text-danger">*</span></label><input type="password" class="form-control" name="config[secret_access_key]" required></div>' +
                '<div class="mb-3"><label class="form-label">Bucket <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[bucket]" required></div>' +
                '<div class="mb-3"><label class="form-label">Region</label><select class="form-select" name="config[region]"><option value="us-east-1">US East 1</option><option value="us-west-1">US West 1</option><option value="eu-central-1">EU Central 1</option><option value="ap-northeast-1">AP Northeast 1</option></select></div>',
            backblaze: '<div class="mb-3"><label class="form-label">Access Key ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_key_id]" required></div>' +
                '<div class="mb-3"><label class="form-label">Secret Access Key <span class="text-danger">*</span></label><input type="password" class="form-control" name="config[secret_access_key]" required></div>' +
                '<div class="mb-3"><label class="form-label">Bucket <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[bucket]" required></div>' +
                '<div class="mb-3"><label class="form-label">Region</label><input type="text" class="form-control" name="config[region]" value="us-west-000" placeholder="us-west-000"></div>',
            cloudflare_r2: '<div class="mb-3"><label class="form-label">Account ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[account_id]" required placeholder="Account ID من Cloudflare"></div>' +
                '<div class="mb-3"><label class="form-label">Access Key ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_key_id]" required></div>' +
                '<div class="mb-3"><label class="form-label">Secret Access Key <span class="text-danger">*</span></label><input type="password" class="form-control" name="config[secret_access_key]" required></div>' +
                '<div class="mb-3"><label class="form-label">Bucket <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[bucket]" required></div>',
            google_drive: '<div class="mb-3"><label class="form-label">Client ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[client_id]" required></div>' +
                '<div class="mb-3"><label class="form-label">Client Secret <span class="text-danger">*</span></label><input type="password" class="form-control" name="config[client_secret]" required></div>' +
                '<div class="mb-3"><label class="form-label">Refresh Token <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[refresh_token]" required></div>' +
                '<div class="mb-3"><label class="form-label">Folder ID (اختياري)</label><input type="text" class="form-control" name="config[folder_id]" placeholder="ID المجلد في Google Drive"></div>',
            bunny: '<div class="mb-3"><label class="form-label">Storage Zone Name <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[storage_zone]" required placeholder="اسم Storage Zone من Bunny"></div>' +
                '<div class="mb-3"><label class="form-label">API Key (FTP Password) <span class="text-danger">*</span></label><input type="password" class="form-control" name="config[api_key]" required placeholder="API Key أو FTP Password"></div>' +
                '<div class="mb-3"><label class="form-label">Region</label><select class="form-select" name="config[region]"><option value="de">DE (Germany)</option><option value="uk">UK (United Kingdom)</option><option value="ny">NY (New York)</option><option value="la">LA (Los Angeles)</option><option value="sg">SG (Singapore)</option><option value="syd">SYD (Sydney)</option><option value="br">BR (Brazil)</option><option value="jh">JH (Johannesburg)</option></select></div>' +
                '<div class="mb-3"><label class="form-label">Pull Zone URL (اختياري)</label><input type="text" class="form-control" name="config[pull_zone]" placeholder="https://your-pull-zone.b-cdn.net"></div>',
            dropbox: '<div class="mb-3"><label class="form-label">Access Token <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_token]" required></div>',
            ftp: '<div class="mb-3"><label class="form-label">Protocol</label><select class="form-select" name="config[protocol]"><option value="ftp">FTP</option><option value="sftp">SFTP</option></select></div>' +
                '<div class="mb-3"><label class="form-label">Host <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[host]" required></div>' +
                '<div class="mb-3"><label class="form-label">Username <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[username]" required></div>' +
                '<div class="mb-3"><label class="form-label">Password <span class="text-danger">*</span></label><input type="password" class="form-control" name="config[password]" required></div>' +
                '<div class="mb-3"><label class="form-label">Port</label><input type="number" class="form-control" name="config[port]" value="21" id="ftp_port"></div>' +
                '<div class="mb-3"><label class="form-label">Root Path</label><input type="text" class="form-control" name="config[root]" value="/"></div>' +
                '<div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="config[use_tls]" value="1" id="use_tls"><label class="form-check-label" for="use_tls">Use TLS</label></div></div>' +
                '<div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="config[passive]" value="1" id="passive" checked><label class="form-check-label" for="passive">Passive Mode</label></div></div>',
            sftp: '<div class="mb-3"><label class="form-label">Host <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[host]" required></div>' +
                '<div class="mb-3"><label class="form-label">Username <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[username]" required></div>' +
                '<div class="mb-3"><label class="form-label">Password <span class="text-danger">*</span></label><input type="password" class="form-control" name="config[password]" required></div>' +
                '<div class="mb-3"><label class="form-label">Port</label><input type="number" class="form-control" name="config[port]" value="22"></div>' +
                '<div class="mb-3"><label class="form-label">Root Path</label><input type="text" class="form-control" name="config[root]" value="/"></div>',
            azure: '<div class="mb-3"><label class="form-label">Account Name <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[account_name]" required></div>' +
                '<div class="mb-3"><label class="form-label">Account Key <span class="text-danger">*</span></label><input type="password" class="form-control" name="config[account_key]" required></div>' +
                '<div class="mb-3"><label class="form-label">Container <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[container]" required></div>',
        };
    }

    function buildEditTemplates(currentConfig) {
        function val(key, defaultValue) {
            if (currentConfig[key] !== undefined && currentConfig[key] !== null) {
                return escapeHtml(currentConfig[key]);
            }
            return defaultValue === undefined ? '' : escapeHtml(defaultValue);
        }

        function checked(key) {
            return currentConfig[key] ? 'checked' : '';
        }

        function selected(key, value) {
            return String(currentConfig[key]) === String(value) ? 'selected' : '';
        }

        return {
            local: '<div class="mb-3"><label class="form-label">المسار (اختياري)</label><input type="text" class="form-control" name="config[path]" value="' + val('path', 'public') + '"></div>',
            s3: '<div class="mb-3"><label class="form-label">Access Key ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_key_id]" value="' + val('access_key_id') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Secret Access Key</label><input type="password" class="form-control" name="config[secret_access_key]" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية"></div>' +
                '<div class="mb-3"><label class="form-label">Bucket <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[bucket]" value="' + val('bucket') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Region</label><input type="text" class="form-control" name="config[region]" value="' + val('region', 'us-east-1') + '"></div>' +
                '<div class="mb-3"><label class="form-label">Endpoint</label><input type="text" class="form-control" name="config[endpoint]" value="' + val('endpoint') + '" placeholder="https://s3.region.amazonaws.com"></div>' +
                '<div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="config[use_path_style]" value="1" id="use_path_style" ' + checked('use_path_style') + '><label class="form-check-label" for="use_path_style">Use Path Style Endpoint</label></div></div>',
            digitalocean: '<div class="mb-3"><label class="form-label">Access Key ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_key_id]" value="' + val('access_key_id') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Secret Access Key</label><input type="password" class="form-control" name="config[secret_access_key]" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية"></div>' +
                '<div class="mb-3"><label class="form-label">Bucket <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[bucket]" value="' + val('bucket') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Region</label><select class="form-select" name="config[region]"><option value="nyc3" ' + selected('region', 'nyc3') + '>NYC3</option><option value="nyc1" ' + selected('region', 'nyc1') + '>NYC1</option><option value="sfo3" ' + selected('region', 'sfo3') + '>SFO3</option><option value="sgp1" ' + selected('region', 'sgp1') + '>SGP1</option><option value="sfo2" ' + selected('region', 'sfo2') + '>SFO2</option><option value="ams3" ' + selected('region', 'ams3') + '>AMS3</option></select></div>',
            wasabi: '<div class="mb-3"><label class="form-label">Access Key ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_key_id]" value="' + val('access_key_id') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Secret Access Key</label><input type="password" class="form-control" name="config[secret_access_key]" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية"></div>' +
                '<div class="mb-3"><label class="form-label">Bucket <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[bucket]" value="' + val('bucket') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Region</label><select class="form-select" name="config[region]"><option value="us-east-1" ' + selected('region', 'us-east-1') + '>US East 1</option><option value="us-west-1" ' + selected('region', 'us-west-1') + '>US West 1</option><option value="eu-central-1" ' + selected('region', 'eu-central-1') + '>EU Central 1</option><option value="ap-northeast-1" ' + selected('region', 'ap-northeast-1') + '>AP Northeast 1</option></select></div>',
            backblaze: '<div class="mb-3"><label class="form-label">Access Key ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_key_id]" value="' + val('access_key_id') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Secret Access Key</label><input type="password" class="form-control" name="config[secret_access_key]" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية"></div>' +
                '<div class="mb-3"><label class="form-label">Bucket <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[bucket]" value="' + val('bucket') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Region</label><input type="text" class="form-control" name="config[region]" value="' + val('region', 'us-west-000') + '" placeholder="us-west-000"></div>',
            cloudflare_r2: '<div class="mb-3"><label class="form-label">Account ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[account_id]" value="' + val('account_id') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Access Key ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_key_id]" value="' + val('access_key_id') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Secret Access Key</label><input type="password" class="form-control" name="config[secret_access_key]" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية"></div>' +
                '<div class="mb-3"><label class="form-label">Bucket <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[bucket]" value="' + val('bucket') + '" required></div>',
            google_drive: '<div class="mb-3"><label class="form-label">Client ID <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[client_id]" value="' + val('client_id') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Client Secret</label><input type="password" class="form-control" name="config[client_secret]" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية"></div>' +
                '<div class="mb-3"><label class="form-label">Refresh Token <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[refresh_token]" value="' + val('refresh_token') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Folder ID (اختياري)</label><input type="text" class="form-control" name="config[folder_id]" value="' + val('folder_id') + '"></div>',
            bunny: '<div class="mb-3"><label class="form-label">Storage Zone Name <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[storage_zone]" value="' + val('storage_zone') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">API Key</label><input type="password" class="form-control" name="config[api_key]" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية"></div>' +
                '<div class="mb-3"><label class="form-label">Region</label><select class="form-select" name="config[region]"><option value="de" ' + selected('region', 'de') + '>DE</option><option value="uk" ' + selected('region', 'uk') + '>UK</option><option value="ny" ' + selected('region', 'ny') + '>NY</option><option value="la" ' + selected('region', 'la') + '>LA</option><option value="sg" ' + selected('region', 'sg') + '>SG</option><option value="syd" ' + selected('region', 'syd') + '>SYD</option><option value="br" ' + selected('region', 'br') + '>BR</option><option value="jh" ' + selected('region', 'jh') + '>JH</option></select></div>' +
                '<div class="mb-3"><label class="form-label">Pull Zone URL</label><input type="text" class="form-control" name="config[pull_zone]" value="' + val('pull_zone') + '"></div>',
            dropbox: '<div class="mb-3"><label class="form-label">Access Token <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[access_token]" value="' + val('access_token') + '" required></div>',
            ftp: '<div class="mb-3"><label class="form-label">Protocol</label><select class="form-select" name="config[protocol]"><option value="ftp" ' + selected('protocol', 'ftp') + '>FTP</option><option value="sftp" ' + selected('protocol', 'sftp') + '>SFTP</option></select></div>' +
                '<div class="mb-3"><label class="form-label">Host <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[host]" value="' + val('host') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Username <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[username]" value="' + val('username') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="config[password]" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية"></div>' +
                '<div class="mb-3"><label class="form-label">Port</label><input type="number" class="form-control" name="config[port]" value="' + val('port', '21') + '"></div>' +
                '<div class="mb-3"><label class="form-label">Root Path</label><input type="text" class="form-control" name="config[root]" value="' + val('root', '/') + '"></div>' +
                '<div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="config[use_tls]" value="1" id="use_tls" ' + checked('use_tls') + '><label class="form-check-label" for="use_tls">Use TLS</label></div></div>' +
                '<div class="mb-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="config[passive]" value="1" id="passive" ' + (checked('passive') || 'checked') + '><label class="form-check-label" for="passive">Passive Mode</label></div></div>',
            sftp: '<div class="mb-3"><label class="form-label">Host <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[host]" value="' + val('host') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Username <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[username]" value="' + val('username') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="config[password]" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية"></div>' +
                '<div class="mb-3"><label class="form-label">Port</label><input type="number" class="form-control" name="config[port]" value="' + val('port', '22') + '"></div>' +
                '<div class="mb-3"><label class="form-label">Root Path</label><input type="text" class="form-control" name="config[root]" value="' + val('root', '/') + '"></div>',
            azure: '<div class="mb-3"><label class="form-label">Account Name <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[account_name]" value="' + val('account_name') + '" required></div>' +
                '<div class="mb-3"><label class="form-label">Account Key</label><input type="password" class="form-control" name="config[account_key]" placeholder="اتركه فارغاً للحفاظ على القيمة الحالية"></div>' +
                '<div class="mb-3"><label class="form-label">Container <span class="text-danger">*</span></label><input type="text" class="form-control" name="config[container]" value="' + val('container') + '" required></div>',
        };
    }

    function collectConfigData(configFields) {
        var configData = {};
        configFields.querySelectorAll('input, select, textarea').forEach(function (input) {
            if (!input.name || !input.name.startsWith('config[')) {
                return;
            }
            var key = input.name.replace('config[', '').replace(']', '');
            if (input.type === 'checkbox') {
                configData[key] = input.checked ? input.value : '';
            } else if (input.type === 'password' && input.value) {
                configData[key] = input.value;
            } else if (input.type !== 'password') {
                configData[key] = input.value || '';
            }
        });
        return configData;
    }

    function showTestResult(resultEl, success, message) {
        resultEl.hidden = false;
        resultEl.className = 'storage-test-result email-form-alert' + (success ? '' : ' email-form-alert--warning');
        resultEl.innerHTML = '<i class="fas fa-' + (success ? 'check-circle' : 'times-circle') + '"></i><span>' + escapeHtml(message) + '</span>';

        if (window.AdminPremium && AdminPremium.showToast) {
            AdminPremium.showToast(message, success ? 'success' : 'error');
        }
    }

    function init() {
        var form = document.getElementById('storage-form');
        if (!form) {
            return;
        }

        var driverSelect = document.getElementById('driver');
        var configFields = document.getElementById('config-fields');
        var testBtn = document.getElementById('test-connection-btn');
        var testResult = document.getElementById('test-connection-result');

        if (!driverSelect || !configFields) {
            return;
        }

        var isEdit = form.hasAttribute('data-current-config');
        var currentConfig = {};
        if (isEdit) {
            try {
                currentConfig = JSON.parse(form.getAttribute('data-current-config') || '{}');
            } catch (error) {
                currentConfig = {};
            }
        }

        var configTemplates = isEdit
            ? buildEditTemplates(currentConfig)
            : buildCreateTemplates();

        function updateConfigFields() {
            var driver = driverSelect.value;
            configFields.style.opacity = '0.5';

            setTimeout(function () {
                if (driver && configTemplates[driver]) {
                    configFields.innerHTML = configTemplates[driver];
                    premiumizeConfigFields(configFields);
                } else if (!driver) {
                    configFields.innerHTML = infoMessage('يرجى اختيار نوع التخزين لعرض الحقول المطلوبة');
                } else {
                    configFields.innerHTML = warningMessage('نوع التخزين المحدد غير مدعوم');
                }
                configFields.style.opacity = '1';
            }, 100);
        }

        driverSelect.addEventListener('change', updateConfigFields);
        updateConfigFields();

        if (testBtn && testResult) {
            testBtn.addEventListener('click', function () {
                var driver = driverSelect.value;
                if (!driver) {
                    showTestResult(testResult, false, 'يرجى اختيار نوع التخزين أولاً');
                    return;
                }

                var originalHtml = testBtn.innerHTML;
                testBtn.disabled = true;
                testBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الاختبار...';
                testResult.hidden = true;

                fetch(form.getAttribute('data-test-url'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        driver: driver,
                        config: collectConfigData(configFields),
                    }),
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        showTestResult(testResult, !!data.success, data.message || (data.success ? 'الاتصال ناجح' : 'فشل الاتصال'));
                    })
                    .catch(function () {
                        showTestResult(testResult, false, 'حدث خطأ أثناء الاختبار');
                    })
                    .finally(function () {
                        testBtn.disabled = false;
                        testBtn.innerHTML = originalHtml;
                    });
            });
        }
    }

    window.StorageForm = { init: init };
})(window);
