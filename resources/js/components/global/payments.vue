<template>
    <v-simple-table
            :dense="true"
    >
        <template v-slot:default>
            <thead>
            <tr>
                <th class="text-center">id</th>
                <th class="text-center">method</th>
                <th class="text-center">date</th>
                <th class="text-center">amount</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(payment, index) in payments" class="text-center" :key="'payment'+index">
                <td>{{'pup-'+payment.paymentable_id +'-'+ payment.id}}</td>
                <td>{{payment.payment_type|payment_by}}</td>
                <td>{{payment.created_at}}</td>
                <td>{{payment.paid}}</td>
            </tr>
            </tbody>
        </template>
    </v-simple-table>
</template>

<script>
    export default {
        name: "payments",
        props:['paymentable_id', 'paymentable_type'],
        data(){
            return {
                payments:[]
            }
        },
        created(){
            axios.get('/api/payments/paymentable_id_type/' + this.paymentable_id+'/'+this.paymentable_type).then(response => {
                this.payments = response.data.payments
            })
        }
    }
</script>

<style scoped>

</style>