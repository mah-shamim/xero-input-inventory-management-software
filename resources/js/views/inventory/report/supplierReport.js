export default {
    data() {
        return {
            items: {},
            loading: false,
            options: {
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            headers: [
                {text: 'company', value: 'company', sortable: false},
                {text: 'id', value: 'code', sortable: false},
                {text: 'name', value: 'name', sortable: false},
                {text: 'address', value: 'address', sortable: false},
                {text: 'phone', value: 'phone', sortable: false},
                {text: 'total purchase', value: 'purchased_total', sortable: false},
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
            let url = '/api/report/suppliers'
            axios.get(url, {params: this.options})
                .then(res => {
                    this.items = res.data
                    this.loading = false
                })
        },
    }
}