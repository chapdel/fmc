document.addEventListener('alpine:init', () => {
    Alpine.data('emailListStatisticsChart', () => ({
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

            new Chart(chart.getContext('2d'), {
                type: "bar",
                data: {
                    labels: this.chartData.labels,
                    datasets: [
                        {
                            label: 'Subscribes',
                            backgroundColor: 'rgba(110, 231, 183, 0.3)',
                            borderColor: 'rgba(110, 231, 183, 1)',
                            pointBackgroundColor: 'rgba(110, 231, 183, 1)',
                            borderRadius: 5,
                            data: this.chartData.subscribes,
                            stack: 'stack0',
                            order: 2,
                        },
                        {
                            label: 'Unsubscribes',
                            backgroundColor: 'rgba(244, 63, 94, 0.1)',
                            borderColor: 'rgba(244, 63, 94, 1)',
                            pointBackgroundColor: 'rgba(244, 63, 94, 1)',
                            borderRadius: 5,
                            data: this.chartData.unsubscribes.map((val) => -val),
                            stack: 'stack0',
                            order: 1,
                        },
                        {
                            label: 'Subscribers',
                            type: 'line',
                            backgroundColor: 'rgba(30, 64, 175, 0.1)',
                            borderColor: 'rgba(30, 64, 175, 1)',
                            pointBackgroundColor: 'rgba(30, 64, 175, 1)',
                            data: this.chartData.subscribers,
                            yAxisID: 'y1',
                            order: 0,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    barPercentage : .70,
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
                        y1: {
                            position: 'right',
                            beginAtZero: false,
                            grid: {
                                display: false,
                            }
                        },
                        x: {
                            ticks: {
                                fontColor: "rgba(30, 64, 175, 1)",
                                callback: function(value, index, ticks) {
                                    return chartData.labels[index].substring(3);
                                }
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
