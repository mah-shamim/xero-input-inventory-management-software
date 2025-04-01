export default {
    data() {
        return {

            forms: this.$root.$data.forms,
            productBonuses: [],
            products: [],
            customerCategories: [],
            method: '',
            button: {
                text: 'Add Product Bonus',
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
            this.productBonuses = []
            this.method = 'Create'
            this.button.text = "Submit"
            this.button.cancel = "Reset"
            this.title.text = "ADD"

        }

    },


    computed: {
        isLoaded() {
            if (this.method === 'Edit') {

                return !_.isEmpty(this.productBonuses)

            } else {
                return true
            }

        }
    },

    created() {
        this.forms.start_date = new Date()
        this.forms.end_date = new Date()
        this.method = this.$root.getMethodAction();
        let url = '/api/inventory/product-bonuses/create';
        if (this.method === 'Edit') {
            url = '/api/inventory/product-bonuses/' + this.$route.params.id + '/edit'
            this.button.text = "Update"
            this.button.cancel = "Cancel"
            this.title.text = "UPDATE"
        }

        this.$root.$data.erp.request.get(url, this, (data) => {
            console.log(data)
            this.products = data.products;
            this.customerCategories = data.customerCategories;
            if (this.method === 'Edit') {
                this.productBonuses = data.productBonuses
                this.forms.id = this.productBonuses.id
                this.forms.product_id = this.productBonuses.product_id
                this.forms.customer_category_id = this.productBonuses.customer_category_id
                this.forms.quantity = this.productBonuses.quantity
                this.forms.bonus_qty = this.productBonuses.bonus_qty
                this.forms.start_date = this.productBonuses.start_date
                this.forms.end_date = this.productBonuses.end_date

            }
        });

    },

    methods: {
        postProductBonus() {
            let requestMethod = 'post'
            let url = '/api/inventory/product-bonuses'
            if (this.method === 'Edit') {
                requestMethod = 'patch'
                url = '/api/inventory/product-bonuses/' + this.forms.id
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
                this.$router.push({name: "productBonusList"})
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