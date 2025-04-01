<template>
    <div>
        <a :data-target="model_name + model_id_data" class="catch-modal dark s" v-if="showPayment">
            Payment
        </a>

        <div :id="model_name + model_id_data" class="modal medium">
            <div class="modal-content">
                <div class="modal-body" style="font-size:13px;">
                    <div class="row">
                        <div class="col-1">
                            <form @submit.prevent="postPayment" @keydown="forms.errors.clear($event.target.name)">
                                <input type="hidden" name="paymentable_id" v-model="model_id_data">
                                <div class="paymentArea">
                                    <div class="form-element inline">
                                        <label for="" class="dark">
                                             Name: <span v-text="name" class="primary"></span>
                                        </label>
                                        
                                    </div>
                                    <div class="form-element">
                                        <label for="" class="dark">Payment Method:</label>
                                        <select name="payment_type" id="" class="input-item"
                                                v-model="forms.payment_type" required>
                                            <option value="1">Cash</option>
                                            <option value="2">Cheque</option>
                                        </select>
                                        <p class="strong danger" v-text="forms.errors.get('payment_type')"></p>
                                    </div>
                                    <!--<div class="form-element ">-->
                                        <!--<label for="" class="dark">Due Amount:</label>-->
                                        <!--<h3 class="warning" v-text="calDue"></h3>-->


                                    <!--</div>-->
                                    <div class="form-element">
                                        <label for="" class="dark">Payment:</label>
                                        <input type="number" step=any class="input-item" name="paid"
                                               v-model="forms.paid"
                                               placeholder="Pay Here"
                                               required="required">
                                        <p class="strong danger" v-text="forms.errors.get('paid')"></p>
                                    </div>
                                    <div class="form-element">
                                        <button type="submit" class="btn bg-dark m">Pay Now!</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: {
            showPayment: {
                type: Boolean,
                default: () => false
            },
            model: {
                type: String,
                default: () => ''
            },
            model_id: {
                type: Number,
                default: () => 0
            },
            list_data: {
                type: Object,
                default: () => []
            }
        },
        data() {
            return {
                model_name: "",
                list: {},
                model_id_data: 0,
                forms: this.$root.$data.forms,
                items: [],
                name: '',
                total: 0,
                paid: 0,
                due: 0,
                data: []
            }
        },
        computed: {
            calDue() {

                let currentDue = this.total - this.paid
                let payment = this.forms.paid ? this.forms.paid : 0
                this.due = (currentDue - payment).toFixed(2)
                return this.due
            }
        },
        created() {
            if (this.showPayment) {

                this.model_name = this.model
                this.model_id_data = this.model_id
                this.list = this.list_data
                this.getName()
                this.total = this.list.total?this.list.total:0
                this.paid = this.list.paid?this.list.paid:0

                /*for (let i = 0; i < this.list.payments.length; i++) {
                    let payment = this.list.payments[i]
                    this.paid += payment.paid
                }*/
            }

        },


        methods: {
            postPayment() {
                let data = Object.assign({}, this.forms)
                let requestMethod = 'post'
                let url = '/api/payments/model/' + this.model_name + '/model_id/' + this.model_id_data + '/payment_id'
                // if (this.method === 'Edit') {
                //     requestMethod = 'patch'
                //     url = '/api/inventory/brands/' + this.forms.id
                // }
                this.forms.submit(requestMethod, url)
                    .then(data => {
                        swal({
                            type: data.type,
                            timer: 2000,
                            text: data.message,
                            showCancelButton: false,
                            showConfirmButton: false,
                        }).catch(swal.noop)
                    }).catch(error => {
                });

            },
            getName() {
                switch (this.model_name) {
                    case "purchases":
                        this.name = this.list.supplier_name
                        break
                    case "sales":
                        this.name = this.list.customer.user.name
                        break
                    default:
                        this.name = "default"
                }
            }
        }
    }
</script>