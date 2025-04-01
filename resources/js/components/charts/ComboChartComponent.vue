<template>
    <div class="chart-container">
        <canvas id="combo" width="100" height="40"></canvas>
    </div>
</template>
<script>
    export default {
        props: ['labels', 'data1', 'data2', 'type1', 'label'],
        mounted() {
            let ctx = document.getElementById('combo')
            new Chart(ctx, {
                type: 'bar',
                data: {
                    datasets: [
                        {
                            label: this.label[0],
                            data: this.data1,
                            backgroundColor: 'rgba(0, 140, 255, 0.450)'
                        },
                        {
                            label: this.label[1],
                            data: this.data2,
                            type: 'line',
                            backgroundColor: 'rgba(177, 177, 177, 0.55)'
                        }
                    ],
                    labels: this.labels
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        }

    }
</script>

<style>

</style>