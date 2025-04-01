import VuetifyDateTime from "./vuetify-date-time.vue";

let date = moment(new Date()).format("YYYY-MM-DD")
export default {
    name: "payment-component-crud",
    components: {VuetifyDateTime},
    props: {
        multipleIds: {
            type: Array,
            default() {
                return []
            }
        },
        model: {
            type: String,
            default: 'purchase'
        },
        model_id: {
            type: Number,
            default: null
        },
        payment_id: {
            type: Number,
            default: null
        },
        method: {
            type: String,
            default: 'create'
        },
        value: {
            type: Boolean,
            required: true,
            default: false
        }
    },
    data: () => ({
        searchValue: null,
        banks: [],
        menu: false,
        loading: false,
        ids: [],
        forms: {
            date: new Date().toISOString().split('T')[0],
            bank_id: '',
            model: 'purchase',
            id: '',
            model_object: null,
            paid: 0,
            payment_type: 1,
            errors: {
                model_object: '',
                id: '',
                date: '',
                payment_type: '',
                paid: '',
                model_id: ''
            }
        }
    }),
    created() {
        this.getBanks()
        if (this.model_id) this.getModelData()
        if (this.payment_id) this.getPaymentData()
        if (this.multipleIds.length > 0) this.getModelDataForMultipleIds()
        if (this.default_payment_type) this.forms.payment_type = Number(this.default_payment_type)
    },
    computed: {
        showModal: {
            get() {
                return !!this.value;
            },
            set(value) {
                if (!value) this.$emit('input', !!value)
            }
        },
        default_payment_type(){
            if(this.model==='purchase'){
                return this.$store.getters["settings/getPurchaseDefaultPaymentMethod"]
            }
            if(this.model==='sale'){
                return this.$store.getters["settings/getSaleDefaultPaymentMethod"]
            }

            return 1
        },
    },
    watch: {
        'forms.model_object'(val) {
            if (!_.isEmpty(val) && this.method === 'create' && _.isEmpty(this.multipleIds)) {
                this.forms.paid = val.paid_total ? val.total - val.paid_total : val.total;
            }
        },
        searchValue(val) {
            if (val && this.method === 'create') {
                this.getValue(val)
            }
        },
    },

    methods: {
        getModelDataForMultipleIds() {
            axios
                .get('/api/payments/crud/create', {
                    params: {
                        multiple: true,
                        model: this.model,
                        model_ids: this.multipleIds,
                    }
                })
                .then(res => {
                    this.ids = res.data
                    this.forms.model_object = res.data
                    let paid_total = 0
                    let amount = 0
                    res.data.forEach(r => {
                        paid_total += r.paid_total
                        amount += r.total
                    })

                    this.forms.paid = amount - paid_total
                })
        },
        getPaymentData() {
            this.ids = []
            axios.get('/api/payments/crud/' + this.payment_id + '/edit', {
                params: {
                    model: this.model
                }
            })
                .then(res => {
                    this.forms.model_object = res.data.paymentable
                    this.ids.push(this.forms.model_object)
                    this.forms.paid = res.data.paid
                    this.forms.payment_type = res.data.payment_type
                    this.forms.bank_id = res.data.transaction.bank_id
                    this.forms.cheque_number = res.data.transaction.cheque_number
                    this.forms.transaction_number = res.data.transaction.transaction_number
                    this.forms.date = moment(res.data.date, this.$store.state.settings.settings.date_format).format("YYYY-MM-DD")
                    this.computedDate = moment(this.forms.date, "YYYY-MM-DD").format(this.$store.state.settings.settings.date_format)
                })
        },
        getModelData() {
            axios
                .get('/api/payments/crud/create', {
                    params: {
                        model: this.model,
                        model_id: this.model_id
                    }
                })
                .then(res => {
                    this.ids = []
                    if (!_.isEmpty(res.data)) {
                        this.ids.push(res.data)
                    }
                    this.forms.model_object = res.data
                })
        },
        resetForm() {
            this.forms = {
                date: moment(new Date()).format("YYYY-MM-DD"),
                bank_id: '',
                model: 'purchase',
                id: '',
                model_object: null,
                paid: 0,
                payment_type: 1,
                errors: {
                    model_object: '',
                    id: '',
                    date: '',
                    payment_type: '',
                    paid: '',
                    model_id: ''
                }
            }
        },

        resetError() {
            this.forms.errors = {
                model_object: '',
                id: '',
                date: '',
                payment_type: '',
                paid: '',
                model_id: ''
            }
        }
        ,
        submitForm() {
            this.forms.model = this.model
            this.resetError()
            this.loading = true
            if (this.payment_id) {
                this.updateData()
            } else {
                this.storeData()
            }
        },
        updateData() {
            axios
                .patch('/api/payments/crud/' + this.payment_id, this.forms)
                .then(res => {
                    swal.fire({
                        type: 'success',
                        text: res.data.message,
                        timer: 3000,
                    })
                        .then((result) => {
                            this.resetForm()
                            this.loading = false
                            this.$emit('paymentSuccess', true)
                        })
                })
                .catch(error => {
                    this.loading = false
                    this.forms.errors = error.response.data.errors
                })
        },
        storeData() {
            if (this.multipleIds.length > 0) {
                this.forms.multiple_ids = this.multipleIds
            }
            axios
                .post('/api/payments/crud', this.forms)
                .then(res => {
                    swal.fire({
                        type: 'success',
                        text: res.data.message,
                        timer: 3000,
                    })
                        .then((result) => {
                            this.resetForm()
                            this.loading = false
                            this.$emit('paymentSuccess', true)
                        })
                })
                .catch(error => {
                    this.loading = false
                    this.forms.errors = error.response.data.errors
                })
        },
        getValue(val) {
            axios
                .get('/api/payments/crud', {
                    params: this.params(val)
                })
                .then(res => {
                    if (!_.isEmpty(res.data)) {
                        this.ids = res.data
                    }
                })
        },
        params(val) {
            let params = {
                model: this.model,
                ref: null,
            }
            if (['expense', 'sale', 'purchase'].includes(this.model)) params.ref = val;

            return params;
        },
        closeDatePicker() {
            let currentTime = moment(new Date()).format("HH:mm:ss")
            let date = this.forms.date
            let selectedDate = moment(date).format(this.$store.state.settings.settings.date_format)
            this.computedDate = moment(selectedDate + ' ' + currentTime, this.$store.state.settings.settings.date_format).format(this.$store.state.settings.settings.date_format)
            this.forms.date = moment(this.forms.date + ' ' + currentTime).format("YYYY-MM-DD")
            this.menu = false
        }
        ,
        getBanks() {
            axios.get('/api/get-bank', {
                params:
                    {
                        with_running_balance: true
                    }
            })
                .then(res => {
                    this.banks = res.data
                    if (!_.isEmpty(this.banks) && this.banks.length === 1) {
                        this.forms.bank_id = this.banks[0].id
                    }
                })
        }
    }
}