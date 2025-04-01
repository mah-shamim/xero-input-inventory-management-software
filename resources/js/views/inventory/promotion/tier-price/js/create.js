export default {
    data() {
        return {

            forms: this.$root.$data.forms,
            tierPrice: [],
            products: [],
            priceCategories: [],
            method: '',
            button: {
                text: 'Add Tier Price',
                cancel: 'Reset'
            },
            title: {
                text: 'ADD'
            },
            loading: false
        }
    },

    beforeCreate() {
        this.$root.$data.forms.fieldReset();
    },

    watch: {
        '$route.fullPath'(val) {
            //reset data
            this.forms.reset();
            this.priceCategories = []
            this.method = 'Create'
            this.button.text = "Submit"
            this.button.cancel = "Reset"
            this.title.text = "ADD"

        }

    },


    computed: {
        isLoaded() {
            if (this.method === 'Edit') {

                return !_.isEmpty(this.priceCategories)

            } else {
                return true
            }

        }
    },

    created() {
        this.forms.start_date = new Date()
        this.forms.end_date = new Date()
        this.method = this.$root.getMethodAction();
        let url = '/api/inventory/product-tires-prices/create';
        if (this.method === 'Edit') {
            url = '/api/inventory/product-tires-prices/' + this.$route.params.id + '/edit'
            this.button.text = "Update"
            this.button.cancel = "Cancel"
            this.title.text = "UPDATE"
        }

        this.$root.$data.erp.request.get(url, this, (data) => {
            this.products = data.products;
            this.priceCategories = data.priceCategories;
            if (this.method === 'Edit') {
                this.tierPrice = data.tierPrice
                this.forms.id = this.tierPrice.id
                this.forms.product_id = this.tierPrice.product_id
                this.forms.price_category_id = this.tierPrice.price_category_id
                this.forms.quantity = this.tierPrice.quantity
                this.forms.price = this.tierPrice.bonus_qty
                this.forms.start_date = this.tierPrice.start_date
                this.forms.end_date = this.tierPrice.end_date

            }
        });

    },

    methods: {
        postProductBonus() {
            let requestMethod = 'post'
            let url = '/api/inventory/product-tires-prices'
            if (this.method === 'Edit') {
                requestMethod = 'patch'
                url = '/api/inventory/product-tires-prices/' + this.forms.id
            }
            this.forms.submit(requestMethod, url, true, this.$root)
                .then(data => {
                    swal({
                        type: data.type,
                        timer: 2000,
                        text: data.message,
                        showCancelButton: false,
                        showConfirmButton: false,
                    }).catch(swal.noop)
                }).catch(error => {
            });
        },
        onCancel() {
            this.forms.reset();
            if (this.method == "Edit") {
                this.$router.push({name: "tierPriceList"})
            }
        },

        getProducts(query) {
            if (query !== '') {
                setTimeout(() => {
                    axios.post('/api/inventory/products/getProducts', {val: query})
                        .then(response => {
                            this.products = response.data.products
                            this.loading = false
                        })
                        .catch(error => {
                            alert(error.message)
                        })
                }, 200);
            } else {
                this.loading = true;
                this.products = []
                return null
            }
        }
    }
}