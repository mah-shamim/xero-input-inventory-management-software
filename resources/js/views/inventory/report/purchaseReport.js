import PurchaseReportPrint from 'g~/print/purchase-report-print.vue'
export default {
    components:{
        PurchaseReportPrint
    },
    data() {
        return {
            forms      : [],
            purchases  : [],
            queryString: {}, /*TODO: relation query string should be initialized with table names and deep nesting later*/
            menu       : false,
            modal      : false,
            items      : {},
            loading    : false,
            warehouses : [],
            options    : {
                itemsPerPage : this.$store.state.itemsPerPage,
                purchase_date: [],
                sortBy       : ['created_at']
            },
            singleLines: [],
            showColumn : false,
            columns    : [],
            headers    : [
                {text: 'date', value: 'purchase_date_formatted', sortable: false, align: ''},
                {text: 'bill no', value: 'bill_no', sortable: true, align: ''},
                {text: 'supplier', value: 'supplier.company', sortable: false, align: ''},
                {text: 'products', value: 'products', sortable: false, align: ''},
                {text: 'warehouse', value: 'warehouse', sortable: false, align: ''},
                {text: 'quantity', value: 'quantity', sortable: false, align: ''},
                {text: 'base quantity', value: 'base_quantity', sortable: false, align: ''},
                {text: 'price', value: 'price', sortable: false, align: ''},
                {text: 'discount', value: 'discount', sortable: false, align: ''},
                {text: 'overall discount', value: 'overall_discount', sortable: false, align: ''},
                {text: 'debit', value: 'total', sortable: false, align: ''},
                {text: 'credit', value: 'credit', sortable: false, align: ''},
                {text: 'balance', value: 'balance', sortable: false, align: ''}
            ],
        }
    },

    computed: {
        totalPurchase() {
            if(this.items && this.items.data) {
                let totals = this.$root.$data.erp.report.purchaseTotalPaidTotal(this.items.data)
                if(totals && totals.totalAmount !== undefined) {
                    return [totals.totalAmount, totals.totalPaid, totals.totalPaid - totals.totalAmount]
                }
            }
        }
    },
    created() {
        if(!_.isEmpty(this.$route.params)) {
            if(this.$route.params.supplier_name !== undefined && this.$route.params.supplier_name) {
                this.options.company = this.$route.params.supplier_name
            }
        }
        this.getWarehouse()
    },
    watch   : {
        options: {
            deep: true,
            handler() {
                this.loading = true
                this.getResults()
            }
        },

    },
    methods : {
        getWarehouse() {
            axios.get('/api/inventory/warehouses?dropdown=true')
                .then(res => {
                    this.warehouses = res.data
                })
        },
        getResults() {
            let url = '/api/report/purchases'
            axios.get(url, {params: this.options})
                .then(res => {
                    this.items = res.data
                    this.items.data.forEach(d => {
                        d.products.forEach(v => {
                            v.warehouse_name = _.find(this.warehouses, {id: v.pivot.warehouse_id}).name
                        })
                    })
                    this.loading = false
                })
        },
        printMe() {
            window.print()
        }
    }
}