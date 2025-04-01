import SaleReportPrint from 'g~/print/sale-report-print.vue'

export default {
    components: {
        SaleReportPrint
    },
    data() {
        return {
            menu: false,
            modal: false,
            items: {},
            loading: false,
            columns: [],
            showColumn: false,
            singleLines: [],
            hideColumns: [],
            options: {
                itemsPerPage: this.$store.state.itemsPerPage,
                sales_date: []
            },
            headers: [
                {text: 'date', value: 'sales_date_formatted', sortable: false},
                {text: 'ref', value: 'ref', sortable: false},
                {text: 'customer', value: 'customer.name', sortable: false},
                {text: 'salesman code', value: 'salesman_code', sortable: false},
                {text: 'products', value: 'products', sortable: false},
                {text: 'quantity', value: 'quantity', sortable: false},
                {text: 'price', value: 'price', sortable: false},
                {text: 'trans type', value: 'trans_type', sortable: false},
                {text: 'discount', value: 'discount', sortable: false},
                {text: 'overall discount', value: 'overall_discount', sortable: false},
                {text: 'debit', value: 'total', sortable: false},
                {text: 'credit', value: 'credit', sortable: false},
                {text: 'balance', value: 'balance', sortable: false}
            ],
        }
    },

    watch: {
        options: {
            deep: true,
            handler() {
                this.loading = true
                this.getResults()
            }
        },
        columns(val) {
            let dontMatch = this.headers.filter((d, i) => {
                return val.indexOf(i) === -1
            })
            let match = this.headers.filter((d, i) => {
                return val.indexOf(i) !== -1
            })

            if (match.length > 0) {
                match.forEach(m => m.align = 'd-none')
            }

            if (dontMatch.length > 0) {
                dontMatch.forEach(m => m.align = '')
            }
        }
    },
    created() {
        if (!_.isEmpty(this.$route.params)) {
            if (this.$route.params.customer_name !== undefined && this.$route.params.customer_name) {
                this.options.customer = this.$route.params.customer_name
            }
        }
    },
    computed: {
        totalPurchase() {
            if (this.items && this.items.data) {
                let totals = this.$root.$data.erp.report.purchaseTotalPaidTotal(this.items.data)
                if (totals && totals.totalAmount !== undefined) {
                    return [
                        (totals.totalAmount).toFixed(4),
                        (totals.totalPaid).toFixed(4),
                        (totals.totalPaid - totals.totalAmount).toFixed(4)
                    ]
                }
            }
        }
    },
    methods: {
        getResults() {
            let url = '/api/report/sales'
            axios.get(url, {params: this.options})
                .then(res => {
                    this.items = res.data
                    this.loading = false
                })
        },

        printMe() {
            window.print()
        }
    }
}