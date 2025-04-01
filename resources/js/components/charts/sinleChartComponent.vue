<template>
    <div class="chart-container" >
        <canvas :id="this.id" width="100" height="40"></canvas>
    </div>
</template>
<script>
    export default {
        props:['labels','data','type','label','id'],
        mounted(){
            // console.log(this.labels, this.data)
            let ctx =document.getElementById(this.id)
            new Chart(ctx, {
                type: this.type,
                data: {
                    labels: this.labels,
                    datasets: [{
                        label: this.label,
                        data: this.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                // padding: 20
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
