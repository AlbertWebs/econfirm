import '../css/app.css';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

document.addEventListener('alpine:init', () => {
    Alpine.data('adminShell', () => ({
        sidebarOpen: false,
        /** True when nav is the slide-over drawer (backdrop only in this mode). */
        isMobileNav: false,
        userMenuOpen: false,
        init() {
            const mq = window.matchMedia('(max-width: 767px)');
            const syncMobileNav = () => {
                this.isMobileNav = mq.matches;
            };
            const syncDrawerBodyClass = () => {
                document.body.classList.toggle(
                    'admin-drawer-open',
                    this.sidebarOpen && this.isMobileNav
                );
            };

            syncMobileNav();
            this.sidebarOpen = !mq.matches;
            syncDrawerBodyClass();
            this.$watch('sidebarOpen', syncDrawerBodyClass);

            mq.addEventListener('change', () => {
                syncMobileNav();
                syncDrawerBodyClass();
                if (!mq.matches) {
                    this.sidebarOpen = true;
                }
            });
        },
        closeSidebar() {
            if (this.isMobileNav) {
                this.sidebarOpen = false;
            }
        },
    }));
});

window.Alpine = Alpine;
Alpine.start();

function initAdminDashboardCharts() {
    const dataEl = document.getElementById('admin-dashboard-charts-data');
    if (!dataEl) {
        return;
    }

    let payload;
    try {
        payload = JSON.parse(dataEl.textContent);
    } catch {
        return;
    }

    const tickColor = '#64748b';
    const gridColor = 'rgba(148, 163, 184, 0.28)';
    const titleColor = '#334155';

    const lineOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(15, 23, 42, 0.92)',
                titleFont: { size: 12 },
                bodyFont: { size: 12 },
                padding: 10,
                cornerRadius: 8,
            },
        },
        scales: {
            x: {
                ticks: { color: tickColor, maxRotation: 40, autoSkip: true, font: { size: 10 } },
                grid: { color: gridColor },
            },
            y: {
                ticks: { color: tickColor, font: { size: 10 }, precision: 0 },
                grid: { color: gridColor },
                beginAtZero: true,
            },
        },
    };

    const traf = document.getElementById('adminChartTraffic');
    if (traf && payload.trafficDaily?.labels?.length) {
        new Chart(traf, {
            type: 'line',
            data: {
                labels: payload.trafficDaily.labels,
                datasets: [{
                    label: 'Page views',
                    data: payload.trafficDaily.values,
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(14, 165, 233, 0.16)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                }],
            },
            options: {
                ...lineOptions,
                plugins: {
                    ...lineOptions.plugins,
                    title: {
                        display: true,
                        text: 'Public page views (last 14 days)',
                        color: titleColor,
                        font: { size: 13, weight: '600' },
                        padding: { bottom: 8 },
                    },
                },
            },
        });
    }

    const esc = document.getElementById('adminChartEscrows');
    if (esc && payload.escrowsDaily?.labels?.length) {
        new Chart(esc, {
            type: 'line',
            data: {
                labels: payload.escrowsDaily.labels,
                datasets: [{
                    label: 'Escrows',
                    data: payload.escrowsDaily.values,
                    borderColor: '#059669',
                    backgroundColor: 'rgba(16, 185, 129, 0.14)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                }],
            },
            options: {
                ...lineOptions,
                plugins: {
                    ...lineOptions.plugins,
                    title: {
                        display: true,
                        text: 'Escrows created (last 14 days)',
                        color: titleColor,
                        font: { size: 13, weight: '600' },
                        padding: { bottom: 8 },
                    },
                },
            },
        });
    }

    const usr = document.getElementById('adminChartUsers');
    if (usr && payload.usersDaily?.labels?.length) {
        new Chart(usr, {
            type: 'bar',
            data: {
                labels: payload.usersDaily.labels,
                datasets: [{
                    label: 'Users',
                    data: payload.usersDaily.values,
                    backgroundColor: 'rgba(59, 130, 246, 0.55)',
                    borderColor: 'rgb(37, 99, 235)',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: {
                ...lineOptions,
                plugins: {
                    ...lineOptions.plugins,
                    title: {
                        display: true,
                        text: 'New user accounts (last 14 days)',
                        color: titleColor,
                        font: { size: 13, weight: '600' },
                        padding: { bottom: 8 },
                    },
                },
            },
        });
    }

    const st = document.getElementById('adminChartStatus');
    const statusSum = (payload.statusBreakdown?.values ?? []).reduce((a, b) => a + b, 0);
    if (st && statusSum > 0) {
        const palette = ['#059669', '#0d9488', '#2563eb', '#7c3aed', '#d97706', '#dc2626', '#64748b', '#0f766e'];
        const bg = payload.statusBreakdown.labels.map((_, i) => palette[i % palette.length]);
        new Chart(st, {
            type: 'doughnut',
            data: {
                labels: payload.statusBreakdown.labels,
                datasets: [{
                    data: payload.statusBreakdown.values,
                    backgroundColor: bg,
                    borderWidth: 1,
                    borderColor: '#fff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Escrows by status',
                        color: titleColor,
                        font: { size: 13, weight: '600' },
                        padding: { bottom: 6 },
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: tickColor,
                            boxWidth: 10,
                            font: { size: 10 },
                            padding: 10,
                        },
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                    },
                },
            },
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdminDashboardCharts);
} else {
    initAdminDashboardCharts();
}
