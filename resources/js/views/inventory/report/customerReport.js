export default {
    data() {
        return {
            items: {},
            loading: false,
            queryString: {},
            options: {
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            headers: [
                {text: 'name', value: 'name', sortable: false},
                {text: 'id', value: 'code', sortable: false},
                {text: 'address', value: 'address', sortable: false},
                {text: 'phone', value: 'phone', sortable: false},
                {text: 'total sale', value: 'sale_total', sortable: false},
                {text: 'total paid', value: 'paid_total', sortable: false},
                {text: 'total due', value: 'due', sortable: false},
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
        }
    },
    methods: {
        getResults() {
            let url = '/api/report/customers'
            axios.get(url, {params: this.options})
                .then(res => {
                    this.items = res.data
                    this.loading = false
                })
        },
        getTotal(customer) {
            let total = 0
            for (let i = 0; i < customer.sales.length; i++) {
                let sale = customer.sales[i]
                total += parseFloat(sale.total)
            }
            return total
        },
        getTotalPaid(customer) {
            let total = 0
            for (let i = 0; i < customer.sales.length; i++) {
                let sale = customer.sales[i]
                for (let j = 0; j < sale.payments.length; j++) {
                    let payment = sale.payments[j]
                    total += parseFloat(payment.paid)
                }
            }
            return total
        }
    }
}