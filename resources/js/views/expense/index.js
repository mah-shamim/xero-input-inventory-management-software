import _               from "lodash"
import ExpenseShow     from './show.vue'
import ExpenseCreate   from './create.vue'
import ExpenseExport   from './expense-export.vue'
import ExpenseBillPaid from './expense-bill-paid.vue'

export default {
    components: {
        ExpenseShow,
        ExpenseExport,
        ExpenseCreate,
        ExpenseBillPaid,
    },
    data() {
        return {
            export_dialog          : false,
            payment_bill_dialog    : false,
            payment_bill_id        : null,
            expenseBillPaidDialog  : false,
            payment_crud_method    : 'create',
            payment_crud_model_id  : null,
            payment_crud_dialog    : false,
            payment_crud_payment_id: null,
            payment_list_dialog    : false,
            expenseShowDialog      : false,
            menu                   : false,
            form_id                : null,
            items                  : {},
            accounts               : [],
            payments               : [],
            loading                : false,
            expenseDialog          : false,
            options                : {
                sortBy      : ['created_at'],
                sortDesc    : [true],
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            headers                : [
                {text: 'bill no', value: 'ref', sortable: true},
                {text: 'warehouse', value: 'warehouse.name', sortable: false},
                {text: 'account', value: 'account.name', sortable: false},
                // {text: 'name-id-type', value: 'transaction.userable.name_id_type', sortable: false},
                {text: 'amount', value: 'amount', sortable: true},
                {text: 'paid', value: 'paid_total', sortable: true},
                {text: 'due', value: 'due', sortable: true},
                {text: 'note', value: 'note', sortable: false},
                // {text: 'created at', value: 'created_at', sortable: true},
                {text: 'bill date', value: 'expense_date', sortable: true},
                {text: 'action', value: 'action', sortable: false},
            ],
            paymentHeaders         : [
                {text: 'amount', value: 'paid'},
                {text: 'date', value: 'date'},
                {text: 'action', value: 'action', action: false},
            ],
        }
    },
    watch     : {
        payment_crud_dialog(val) {
            if(!val) {
                this.resetPaymentComponent()
            }
        },
        options: {
            deep: true,
            handler() {
                this.loading = true
                this.getData()
            }
        },
        expenseDialog(val) {
            if(!val) {
                this.form_id = null
                this.getData()
            }
        }
    },
    created() {
        if(!_.isEmpty(this.$route.params) && this.$route.params.id) {
            this.options.id = this.$route.params.id
        }
        this.getAccounts()
    },
    methods   : {
        uploadFile() {
            let formData = new FormData()
            let file     = this.$refs.inputUpload.files[0]
            formData.append('file', file)
            axios.post('/api/inventory/expense-import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(res => {
                    swal.fire({
                        icon: 'success',
                        text: 'file has been uploaded successfully, please update payments as well'
                    }).then((result)=>{
                        this.getData()
                    })
                })
                .catch(error => {
                    swal.fire({
                        icon: 'error',
                        html: 'status '+error.response.status+'.<br>'+error.response.statusText,
                    })
                })
        },
        closeDialogExport(val) {
            if(val === 200) {
                this.export_dialog = false
            }
        },
        resetPaymentComponent() {
            this.payment_crud_method     = 'create'
            this.payment_crud_model_id   = null
            this.payment_crud_payment_id = null
        },
        editBill(id) {
            this.payment_crud_payment_id = id
            this.payment_crud_method     = 'edit'
            this.payment_crud_dialog     = true
            this.payment_list_dialog     = false
        },
        paymentSuccess(val) {
            if(val) {
                this.getData()
                this.payment_crud_dialog = false
            }
        },
        showExpense(id) {
            this.form_id           = id
            this.expenseShowDialog = true
        },
        editExpense(id) {
            this.form_id       = id
            this.expenseDialog = true
        },
        deleteItem(id) {
            this.loading = true
            this.$deleteWithConfirmation({
                text: 'Are you sure you want delete this expense?',
                url : '/api/expenses/' + id
            })
                .then(data=>{
                    this.loading = false
                    this.getData()
                })

            this.loading = true

        },
        getAccounts() {
            axios.get('/api/accounts/get-accounts-by-parent/Expense')
                .then(res => {
                    this.accounts = res.data
                })

        },
        getData: _.debounce(function () {
            this.loading = true
            axios.get('/api/expenses', {
                params: this.options
            })
                .then(res => {
                    this.items   = res.data.expenses
                    this.loading = false
                })
        }, 800),
        getExpenseList(query, callback) {
            axios.get('/api/expenses?dropdown=true').then(response => {
                this.expenses = response.data;
                callback(query);
            }).catch(error => {

            })
        },
        createExpenseBillById(id) {
            this.method                = 'create'
            this.payment_crud_model_id = id
            this.payment_crud_dialog   = true
        },
        getListOfExpenseBills(id) {
            this.payments = []
            axios
                .get('/api/payments/expense-bill-paid', {
                    params: {model_id: id}
                })
                .then(res => {
                    this.payments = res.data
                    if(this.payments.length > 0) {
                        this.payment_list_dialog = true
                    }
                })
        },
        fromCreate(val) {
            console.log(val)
            if(val.type === 'success') {
                if(val.show_payment_component) {
                    this.createExpenseBillById(val.expense.id)
                }
                this.expenseDialog = false
                if(!val.show_payment_component) {
                    if(val.method !== undefined && val.method !== 'edit') {
                        setTimeout(e => {
                            this.expenseDialog = true
                        }, 200)
                    }
                }
            }
        },
        resetQuery() {
            this.options              = {
                sortDesc    : [true],
                sortBy      : ['created_at'],
                itemsPerPage: this.$store.state.itemsPerPage,
            }
            this.options.ref          = null
            this.options.warehouse    = null
            this.options.amount       = null
            this.options.account_id   = null
            this.options.expense_date = null
        }
    }
}