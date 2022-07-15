document.addEventListener('alpine:init', () => {
    Alpine.data('dashboardChart', () => ({
        chartData: {},
        renderChart: function(chartData) {
            const chart = document.getElementById('chart');

            this.chartData = chartData;

            let c = false;

            Chart.helpers.each(Chart.instances, function(instance) {
                if (instance.canvas.id === 'chart') {
                    c = instance;
                }
            });

            if (c) {
                c.destroy();
            }

            new Chart(chart.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: this.chartData.labels,
                    datasets: [
                        {
                            label: 'Subscribes',
                            backgroundColor: 'rgb(49 46 129 / 0.2)',
                            hoverBackgroundColor: 'rgb(49 46 129 / 0.4)',
                            borderRadius: 5,
                            data: this.chartData.subscribes,
                            stack: 'stack0',
                            order: 2,
                        },
                        {
                            label: 'Unsubscribes',
                            backgroundColor: '#ef444466',
                            hoverBackgroundColor: '#ef4444cc',
                            borderRadius: 5,
                            data: this.chartData.unsubscribes.map(val => (val ? -val : 0)),
                            stack: 'stack0',
                            order: 1,
                        },
                        {
                            label: 'Subscribers',
                            type: 'line',
                            borderColor: '#1d4ed8',
                            pointBackgroundColor: '#2563eb',
                            pointBorderColor: '#2563eb',
                            data: this.chartData.subscribers,
                            yAxisID: 'y1',
                            order: 0,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    barPercentage: 0.75,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        zoom: {
                            pan: {
                                enabled: true,
                                mode: 'x',
                                modifierKey: 'ctrl',
                            },
                            zoom: {
                                drag: {
                                    enabled: true,
                                },
                                mode: 'x',
                            },
                        },
                        legend: {
                            display: true,
                        },
                        tooltip: {
                            backgroundColor: 'rgba(37, 42, 63, 1)',
                            titleSpacing: 4,
                            bodySpacing: 8,
                            padding: 20,
                            displayColors: false,
                            callbacks: {
                                afterBody: tooltips => {
                                    const campaigns = this.chartData.campaigns[tooltips[0].dataIndex];

                                    if (campaigns.length === 0) {
                                        return;
                                    }

                                    return `Campaign${campaigns.length > 1 ? 's' : ''}: ${campaigns
                                        .map(campaign => campaign.name)
                                        .join(', ')}`;
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: 'rgba(100, 116, 139, 1)',
                                precision: 0,
                            },
                            grid: {
                                display: false,
                            },
                        },
                        y1: {
                            ticks: {
                                color: 'rgba(100, 116, 139, 1)',
                            },
                            position: 'right',
                            beginAtZero: false,
                            grid: {
                                display: false,
                            },
                        },
                        x: {
                            ticks: {
                                autoSkip: true,
                                maxRotation: 0,
                                color: 'rgba(100, 116, 139, 1)',
                            },
                            grid: {
                                borderColor: 'rgba(100, 116, 139, .2)',
                                borderDash: [5, 5],
                                zeroLineColor: 'rgba(100, 116, 139, .2)',
                                zeroLineBorderDash: [5, 5],
                            },
                        },
                    },
                },
            });
        },
    }));
});
