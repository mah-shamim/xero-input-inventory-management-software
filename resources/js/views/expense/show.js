import ExpenseShowPrint from './expense-show-print.vue'
export default {
    props  : {
        form_id: null
    },
    components:{
        ExpenseShowPrint
    },

    data   : () => ({
        item          : {
            warehouse  : {},
            transaction: {
                bank    : {},
                account : {},
                userable: {}
            },
        },
        paymentHeaders: [
            {text: 'date', value: 'date', sortable: true},
            {text: 'paid', value: 'paid', sortable: true},
            {text: 'mood', value: 'mood', sortable: true},
            {text: 'bank', value: 'transaction.bank.name', sortable: true},
        ]
    }),
    created() {
        if(this.form_id) {
            this.getData()
        }
    },
    methods: {
        getData() {
            axios
                .get('/api/expenses/' + this.form_id)
                .then(res => {
                    this.item = res.data
                })
        }
    }
}