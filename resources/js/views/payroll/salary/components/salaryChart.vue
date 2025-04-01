<template>
    <div>
        <pie-chart :chart-data="datacollection" v-if="loaded" :height="110"></pie-chart>
    </div>
</template>

<script>
    import PieChart from './PieChart.js'

    export default {
        props:['total'],
        components: {
            PieChart
        },
        data() {
            return {
                loaded: false,
                datacollection: {
                    labels:[],
                    datasets:[]
                }
            }
        },
        mounted() {
            this.loaded=false
            let dataset1 = {data:[], backgroundColor: ['rgba(248,121,121,0.64)','rgba(64,85,248,0.73)','rgba(0,248,47,0.73)']}
            let labels = ['total salary', 'total paid', 'total due']
            dataset1.data.push(this.total.total_current_salary)
            dataset1.data.push(this.total.total_paid)
            dataset1.data.push(this.total.total_due)
            this.datacollection.labels = labels
            this.datacollection.datasets.push(dataset1)
            this.loaded=true
        },
    }
</script>