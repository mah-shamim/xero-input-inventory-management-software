<template v-if="paymentable_id">
    <v-card>
        <v-card-text>
            {{paymentable_type}} Total: {{this.total}}
        </v-card-text>
        <form>
            <v-col cols="12" md="12">
                <v-select v-model="payment_type"
                          :items="methods"
                          item-text="name"
                          item-value="id"
                          label="method"
                          name="payment_type"
                          v-validate="'required'"
                          :error-messages="errors.collect('payment_type')"
                          data-vv-name="payment_type"
                ></v-select>
            </v-col>
            <v-col cols="12" md="12">
                <v-text-field
                        v-model="paid"
                        label="due amount"
                        name="paid"
                        v-validate="getBetween"
                        :error-messages="errors.collect('paid')"
                        data-vv-name="paid"
                        type="number"
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="12" v-if="payment_type===3">
                <v-text-field v-model="transaction_number" label="cheque number"></v-text-field>
            </v-col>
            <v-col cols="12" md="12">
                <v-btn @click="postPayment">submit</v-btn>
            </v-col>
        </form>
    </v-card>
</template>
<script>
    export default {
        name: 'payment-add',
        props: ['paymentable_id', 'paymentable_type','total'],
        data: () => ({
            payment_type: null,
            transaction_number: null,
            paid: null,
            due: null,
            methods: [
                {id: 1, name: 'cash'},
                {id: 2, name: 'credit card'},
                {id: 3, name: 'cheque'}
            ]
        }),
        computed: {
            getBetween() {
                if (this.paid) {
                    return "required|between:0," + `${Math.abs(this.due)}`
                }
            }
        },
        created() {
            axios.get('/api/payments/paymentable_id_type/' + this.paymentable_id + '/' + this.paymentable_type)
                .then(response => {
                    let payments = response.data.payments
                    this.due = this.$root.$data.erp.report.checkPaymentStatus(this.total, payments)
                    this.paid = Math.abs(this.due)
                })
        },
        methods: {
            postPayment() {
                this.$validator.validateAll().then(result => {
                    if (result) {
                        if (this.due < 0) {
                            axios.post('/api/payments/model/' + this.paymentable_type + '/model_id/' + this.paymentable_id + '/payment_id', {
                                'paid': this.paid,
                                'transaction_number': this.transaction_number,
                                'payment_type': this.payment_type
                            })
                                .then(res => {
                                    swal({
                                        type: res.data.type,
                                        timer: 2000,
                                        text: res.data.message,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                    }).catch(swal.noop)
                                }).catch(error => {
                            })
                            this.$emit('add_payment_dialog', false)
                        } else {
                            swal({
                                type: 'error',
                                timer: 4000,
                                text: 'payment is cleared already on that ' + this.paymentable_type,
                                showCancelButton: false,
                                showConfirmButton: false,
                            }).catch(swal.noop)
                        }
                    }
                })
            }
        }
    }
</script>