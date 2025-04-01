import AProductLocationsList from 'g~/a-product-locations-list.vue'
export default {
    components:{
        AProductLocationsList
    },
    data() {
        return {
            name                   : '',
            code                   : '',
            queryString            : {},
            account_method         : '',
            menu                   : false,
            modal                  : false,
            items                  : {},
            loading                : false,
            product_location_dialog: false,
            location_product_id    : null,
            options                : {
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            headers                : [
                {text: 'Name', value: 'name', sortable: false},
                {text: 'Code', value: 'code', sortable: false},
                {text: 'Remaining Quantity', value: 'remaining_quantity', sortable: false},
                {text: 'Weight', value: 'remaining_weight', sortable: false},
                {text: 'Total Purchased', value: 'purchased_total', sortable: false},
                {text: 'Total Sold', value: 'sold_total', sortable: false}
            ],
        }
    },
    created() {
        this.account_method = this.$root.$data.erp.report.settings().settings.inventory.account_method
    },
    watch  : {
        options: {
            deep: true,
            handler() {
                this.loading = true
                this.getResults()
            }
        },
        product_location_dialog(val) {
            if(!val) {
                this.location_product_id = null
            }
        },
    },
    methods: {
        getResults() {
            let url = '/api/report/products'
            axios.get(url, {params: this.options})
                .then(res => {
                    this.items   = res.data
                    this.loading = false
                })
        }
    }
}
