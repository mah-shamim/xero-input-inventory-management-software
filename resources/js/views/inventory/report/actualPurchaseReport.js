export default {
    data() {
        return {
            forms: [],
            laravelData: {},
            purchases: [],
            queryString: {}, /*TODO: relation query string should be initialized with table names and deep nesting later*/
            purchase_date: [],
            ref: '',
            name: '',
            totalPageNumber: 10,
            pageSagement: 100,
            products: ''
        }
    },

    computed: {
        totalPurchase() {
            let totals = this.$root.$data.erp.report.purchaseTotalPaidTotal(this.purchases, 0, 0)
            return [totals.totalAmount, totals.totalPaid, totals.totalPaid - totals.totalAmount]
        }
    },

    watch: {
        products(vals) {
            if (vals) {
                this.queryString.rel = {products: {name: ""}}
                this.queryString.rel.products.name = vals
            } else {
                delete this.queryString.rel.products
            }
            this.getResults()
        },

        totalPageNumber(vals) {
            this.totalPageNumber = vals
            this.getResults()
        },
        name(vals) {
            if (vals) {
                this.queryString.rel = {supplier: {company: ""}};
                this.queryString.rel.supplier.company = vals
            } else {
                delete this.queryString.rel.supplier
            }

            this.getResults()
        },
        purchase_date(val) {
            if (_.size(val) > 0) {
                if (!val[0]) {
                    delete this.queryString.purchase_date
                } else {
                    this.queryString.purchase_date = []
                    var fromDate = moment(val[1]).format("YYYY-MM-DD HH:mm:ss")
                    if (moment(val[1]).format('HH:mm:ss') == '00:00:00') {
                        fromDate = moment(val[1]).format("YYYY-MM-DD") + ' 23:59:59'
                    }

                    let date = [moment(val[0]).format("YYYY-MM-DD HH:mm:ss"), fromDate];
                    this.queryString.purchase_date = date
                }
                this.getResults()
            }
        },
        ref(val) {
            if (val) {
                this.queryString.ref = ''
                this.queryString.ref = val
            } else {
                delete this.queryString.ref
            }

            this.getResults()

        }
    },
    created() {
        this.getResults()
    },
    methods: {
        getResults(page) {
            if (typeof page === 'undefined') {
                page = 1
            }

            let url = '/api/report/actualpurchases?page=' + page + '&query=' + JSON.stringify(this.queryString) + '&totalPage=' + this.totalPageNumber;

            this.$root.$data.erp.request.get(url, this, (data) => {
                this.laravelData = data
                this.purchases = this.laravelData.data
            });
        },
        printMe(){
            window.print()
        }
    }
}