import _ from 'lodash'

export default {
    props: {
        value: {
            type: Boolean,
            required: true,
            default: false
        },
        id: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            pay_later: false,
            banks: [],
            forms: {
                expense_date: new Date().toISOString().split('T')[0],
                ref: '',
                amount: 0.0,
                account_id: null,
                warehouse_id: null,
                userable_id: null,
                payment_method: 3,
                note: ''
            },
            expenses: [],
            warehouses: [],
            // categories   : [],
            method: '',
            loading: false,
            modal: false,
            menu1: false,
            menu2: false,
            computedDate: null,
            computedDate1: null,
            users: [],
            searchItems: null,
            accounts: [],
        }
    },

    watch: {
        searchItems: {
            immediate: true,
            handler(val) {
                this.getUsers(val)
            }
        },
        id: {
            // immediate: true,
            // handler(val) {
            //     if (val) this.editItem(val)
            // }
        }
    },

    computed: {
        showModal: {
            get() {
                return this.value;
            },
            set(value) {
                if (!value) this.$emit('input', value)
            }
        },
        isLoaded() {
            return this.method === 'Edit' ? !_.isEmpty(this.expenses) : true
        }
    },

    created() {
        this.loading = true

        this.method = this.$root.getMethodAction();
        let url = '/api/expenses/create';
        if (this.id) {
            this.method = 'Edit'
            url = '/api/expenses/' + this.id + '/edit'
        }
        axios.get(url)
            .then(res => {
                this.warehouses = res.data.warehouses
                if (this.warehouses.length === 1) {
                    this.forms.warehouse_id = this.warehouses[0].id
                }
                this.banks = res.data.banks
                this.accounts = res.data.accounts
                this.getUsers()
                if (this.method === 'Edit') {
                    this.expenses = res.data.expenses
                    this.forms.id = this.expenses.id
                    // this.forms.expense_date   = moment(new Date(this.expenses.expense_date)).format('YYYY-MM-DD')
                    this.forms.expense_date = moment(this.expenses.expense_date, this.$store.getters['settings/getDefaultDateFormat']).format("YYYY-MM-DD")

                    this.forms.warehouse_id = parseInt(this.expenses.warehouse_id)
                    // this.forms.category_id    = parseInt(this.expenses.category_id)
                    this.forms.payment_method = parseInt(this.expenses.payment_method)
                    this.forms.ref = this.expenses.ref
                    this.forms.amount = this.expenses.amount
                    this.forms.note = this.expenses.note
                    if (res.data.expenses.account_id) {
                        this.forms.account_id = this.expenses.account_id

                        axios.get('/api/get-any-user/' + this.expenses.userable_id, {
                            params: {
                                model: this.expenses.userable_type
                            }
                        })
                            .then(res => {
                                this.forms.userable_id = res.data
                            })
                    }
                }
                this.loading = false
            })

    },

    methods: {
        getUsers: _.debounce(function (query) {
            axios
                .get("/api/get-any-user", {
                    params: {name: query},
                })
                .then((result) => {
                    this.users = result.data;
                })
        }, 200),

        postExpense() {
            this.forms.expense_date = moment(this.forms.expense_date).format("YYYY-MM-DD HH:mm:ss")
            let requestMethod = 'post'
            let url = '/api/expenses'
            if (this.method === 'Edit') {
                requestMethod = 'patch'
                url = '/api/expenses/' + this.forms.id
            }
            axios[requestMethod](url, this.forms)
                .then(res => {
                    this.loading = false
                    swal.fire({
                        type: res.data.type,
                        timer: 2000,
                        text: res.data.message,
                        showCancelButton: false,
                        showConfirmButton: false,
                    })
                    this.forms.expense_date = moment(new Date()).format('YYYY-MM-DD')
                    res.data['show_payment_component'] = !(this.id || this.pay_later);
                    if (this.id) {
                        res.data['method'] = 'edit'
                    }
                    this.$emit('fromCreate', res.data)
                })
                .catch(error => {
                    console.log(error)
                    let err
                    if(error.response){
                        let errs = error.response.data.errors
                        for (err in errs) {
                            this.errors.add({
                                'field': err,
                                'msg': errs[err][0],
                            })
                        }
                    }

                })
        }
    }
}