(function () {
    'use strict';

    var data = window.dashboardData || {};
    var chartInstances = [];
    var arLocale = null;

    function isDarkMode() {
        return document.documentElement.getAttribute('data-theme-mode') === 'dark';
    }

    function chartTheme() {
        var dark = isDarkMode();
        return {
            gridBorder: dark ? 'rgba(255, 255, 255, 0.08)' : '#e2e8f0',
            labelColor: dark ? 'rgba(255, 255, 255, 0.6)' : '#64748b',
            tooltipTheme: dark ? 'dark' : 'light',
            legendColor: dark ? 'rgba(255, 255, 255, 0.85)' : '#1e293b',
        };
    }

    function destroyCharts() {
        chartInstances.forEach(function (chart) {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        chartInstances = [];
    }

    function animateCounter(el) {
        var target = parseFloat(el.getAttribute('data-count')) || 0;
        var decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
        var duration = 1200;
        var start = performance.now();

        function step(now) {
            var progress = Math.min((now - start) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            var current = target * eased;
            el.textContent = current.toLocaleString('ar-EG', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            });
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }

        requestAnimationFrame(step);
    }

    function initCounters() {
        document.querySelectorAll('.dash-kpi-value[data-count]').forEach(animateCounter);
    }

    function baseChartOptions(height) {
        var theme = chartTheme();
        return {
            chart: {
                fontFamily: 'Cairo, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false },
                height: height,
                rtl: true,
                background: 'transparent',
            },
            grid: {
                borderColor: theme.gridBorder,
                strokeDashArray: 4,
            },
            tooltip: {
                theme: theme.tooltipTheme,
                style: { fontFamily: 'Cairo, sans-serif' },
            },
            dataLabels: { enabled: false },
        };
    }

    function initSalesChart() {
        var el = document.querySelector('#dash-sales-chart');
        if (!el || typeof ApexCharts === 'undefined') return;

        el.innerHTML = '';

        var theme = chartTheme();
        var labels = data.salesChart?.labels || [];
        var sales = data.salesChart?.totals || [];
        var purchases = data.purchasesChart?.totals || [];

        var options = Object.assign({}, baseChartOptions(320), {
            series: [
                { name: 'المبيعات', data: sales },
                { name: 'المشتريات', data: purchases },
            ],
            chart: Object.assign({}, baseChartOptions(320).chart, {
                type: 'area',
                locales: arLocale ? [arLocale] : [],
                defaultLocale: arLocale ? 'ar' : undefined,
            }),
            colors: ['#4f46e5', '#ef4444'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: isDarkMode() ? 0.45 : 0.35,
                    opacityTo: 0.05,
                    stops: [0, 90, 100],
                },
            },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: labels,
                labels: {
                    style: {
                        fontFamily: 'Cairo, sans-serif',
                        colors: theme.labelColor,
                    },
                },
                axisBorder: { color: theme.gridBorder },
                axisTicks: { color: theme.gridBorder },
            },
            yaxis: {
                labels: {
                    style: {
                        fontFamily: 'Cairo, sans-serif',
                        colors: theme.labelColor,
                    },
                    formatter: function (val) {
                        return val.toLocaleString('ar-EG');
                    },
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontFamily: 'Cairo, sans-serif',
                labels: { colors: theme.legendColor },
            },
        });

        var chart = new ApexCharts(el, options);
        chart.render();
        chartInstances.push(chart);
    }

    function initBarChart(elementId, chartData, color, emptyMessage) {
        var el = document.querySelector(elementId);
        if (!el || typeof ApexCharts === 'undefined') return;

        el.innerHTML = '';

        var labels = chartData?.labels || [];
        var values = chartData?.values || [];

        if (!labels.length) {
            el.innerHTML =
                '<div class="dash-empty-chart"><i class="fas fa-chart-bar"></i><span>' +
                emptyMessage +
                '</span></div>';
            return;
        }

        var theme = chartTheme();

        var options = Object.assign({}, baseChartOptions(280), {
            series: [{ name: 'القيمة', data: values }],
            chart: Object.assign({}, baseChartOptions(280).chart, { type: 'bar' }),
            colors: [color],
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    barHeight: '65%',
                },
            },
            xaxis: {
                categories: labels,
                labels: {
                    style: {
                        fontFamily: 'Cairo, sans-serif',
                        colors: theme.labelColor,
                    },
                    formatter: function (val) {
                        return Number(val).toLocaleString('ar-EG');
                    },
                },
                axisBorder: { color: theme.gridBorder },
                axisTicks: { color: theme.gridBorder },
            },
            yaxis: {
                labels: {
                    style: {
                        fontFamily: 'Cairo, sans-serif',
                        fontSize: '12px',
                        colors: theme.labelColor,
                    },
                    maxWidth: 120,
                },
            },
            tooltip: {
                theme: theme.tooltipTheme,
                y: {
                    formatter: function (val) {
                        return val.toLocaleString('ar-EG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    },
                },
            },
        });

        var chart = new ApexCharts(el, options);
        chart.render();
        chartInstances.push(chart);
    }

    function initCharts() {
        destroyCharts();
        initSalesChart();
        initBarChart('#dash-products-chart', data.topProductsChart, '#4f46e5', 'لا توجد بيانات للمنتجات');
        initBarChart('#dash-customers-chart', data.topCustomersChart, '#3b82f6', 'لا توجد بيانات للعملاء');
    }

    function loadLocaleAndInit() {
        var localeUrl = data.localeUrl;
        if (localeUrl) {
            fetch(localeUrl)
                .then(function (r) { return r.json(); })
                .then(function (locale) {
                    arLocale = locale;
                    initCharts();
                })
                .catch(function () {
                    initCharts();
                });
        } else {
            initCharts();
        }
    }

    function watchThemeChanges() {
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.attributeName === 'data-theme-mode') {
                    initCharts();
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme-mode'],
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initCounters();
        loadLocaleAndInit();
        watchThemeChanges();
    });
})();
