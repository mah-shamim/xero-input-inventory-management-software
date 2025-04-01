import PurchaseShowPrint from './purchase-show-print.vue'
import ReturnPurchase from './returns.vue'

export default {
    props: {
        value: {
            type: Number,
            default: null
        },
    },
    components: {
        ReturnPurchase,
        PurchaseShowPrint
    },
    data() {
        return {
            payment_crud_method: 'create',
            payment_crud_dialog: false,
            payment_crud_model_id: null,
            payment_crud_payment_id: null,
            dialog: false,
            forms: {},
            return_id: null,
            purchase: {
                supplier: {},
                payments: [],
                returns: []
            },
            company: {
                name: {}
            },
            totalRAmount: 0,
        }
    },
    created() {
        this.getData()
    },
    watch: {
        payment_crud_dialog(val) {
            if (!val) {
                this.resetPaymentComponent()
            }
        },
        return_id(val){
            if(!val){
                this.getData()
            }
        }
    },
    computed: {
        product_total() {
            if (!_.isEmpty(this.purchase) && !_.isEmpty(this.purchase.products)) {
                return this.purchase.products.reduce((acc, obj) => {
                    return acc + obj.pivot.subtotal
                }, 0)
            }
            return 0
        },
        sub_total() {
            return this.product_total + this.purchase.shipping_cost
        },
        discounted_amount() {
            return (this.sub_total * (this.purchase.overall_discount / 100)).toFixed(4)
        },
        showModal: {
            get() {
                return !!this.value;
            },
            set(value) {
                if (!value) this.$emit('input', null)
            }
        }
    },
    methods: {
        createBill() {
            this.resetPaymentComponent()
            this.payment_crud_model_id = this.purchase.id
            this.payment_crud_dialog = true
        },
        editBill(id) {
            this.payment_crud_payment_id = id
            this.payment_crud_method = 'edit'
            this.payment_crud_dialog = true
            this.payment_list_dialog = false
        },
        resetPaymentComponent() {
            this.payment_crud_method = 'create'
            this.payment_crud_model_id = null
            this.payment_crud_payment_id = null
        },
        paymentSuccess(val) {
            if (val) {
                this.getData()
                this.payment_crud_dialog = false
            }
        },
        getData() {
            axios.get(api_base_url + '/purchases/' + this.value)
                .then(res => {
                    let data = res.data
                    this.purchase = data.purchase
                    this.company = data.company
                    this.totalRAmount = 0
                    if (this.purchase.returns.length > 0) {
                        for (let i = 0; i < this.purchase.returns.length; i++) {
                            this.totalRAmount += parseFloat(this.purchase.returns[i].amount)
                        }
                    }
                })
        },
        showAmountEdit(item, index) {
            this.forms = item
            this.forms.price = item.product.buying_price
            this.dialog = true
        },
    }
}