document.addEventListener('alpine:init', () => {
    Alpine.data('campaignStatisticsChart', () => ({
        chartData: {},
        renderChart: function(chartData){
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

            const lineOptions = {
                fill: true,
                cubicInterpolationMode: 'monotone',
                pointRadius: 1,
                pointHoverRadius: 5
            }

            new Chart(chart.getContext('2d'), {
                type: "line",
                data: {
                    labels: this.chartData.labels,
                    datasets: [
                        {
                            ...lineOptions,
                            label: 'Opens',
                            backgroundColor: 'rgba(30, 64, 175, 0.1)',
                            borderColor: 'rgba(30, 64, 175, 1)',
                            pointBackgroundColor: 'rgba(30, 64, 175, 1)',
                            data: this.chartData.opens,
                        },
                        {
                            ...lineOptions,
                            label: 'Clicks',
                            backgroundColor: 'rgba(110, 231, 183, 0.1)',
                            borderColor: 'rgba(110, 231, 183, 1)',
                            pointBackgroundColor: 'rgba(110, 231, 183, 1)',
                            data: this.chartData.clicks,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
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
                                    enabled: true
                                },
                                mode: 'x',
                            },
                        },
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: 'rgba(30, 64, 175, 0.8)',
                            titleSpacing: 4,
                            bodySpacing: 4,
                            padding: 8,
                            displayColors: false,
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                fontColor: "rgba(30, 64, 175, 1)",
                            },
                            grid: {
                                display: false,
                            },
                        },
                        x: {
                            ticks: {
                                fontColor: "rgba(30, 64, 175, 1)",
                            },
                            grid: {
                                borderColor: "rgba(30, 64, 175, .2)",
                                borderDash: [5, 5],
                                zeroLineColor: "rgba(30, 64, 175, .2)",
                                zeroLineBorderDash: [5, 5]
                            },
                        }
                    }
                }
            });
        }
    }));
});
