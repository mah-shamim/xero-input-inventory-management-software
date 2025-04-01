export default {
    data() {
        return {

            forms: this.$root.$data.forms,
            warranty: [],
            statuses: ['Receive from Customer',
                'Send to Supplier',
                'Receive from Supplier',
                'Delivered to Customer',
                'Damaged'],
            customers: [],
            products: [],
            method: '',
            button: {
                text: 'Add Warranty',
                cancel: 'Reset'
            },
            title: {
                text: 'ADD'
            },
            loadingMessage:false,
        }
    },

    beforeCreate() {
        this.$root.$data.forms.fieldReset();
    },

    watch: {
        '$route.fullPath'(val) {
            //reset data
            this.forms.reset();
            this.warranty = []
            this.method = 'Create'
            this.button.text = "Submit"
            this.button.cancel = "Reset"
            this.title.text = "ADD"


        }

    },


    computed: {
        isLoaded() {
            if (this.method === 'Edit') {
                return !_.isEmpty(this.warranty)
            } else {
                return true
            }

        }
    },

    created() {
        this.forms.warranty_date = new Date()
        this.method = this.$root.getMethodAction();
        let url = '/api/inventory/warranty/create';
        if (this.method === 'Edit') {
            url = '/api/inventory/warranty/' + this.$route.params.id + '/edit'
            this.button.text = "Update"
            this.button.cancel = "Cancel"
            this.title.text = "UPDATE"
        }

        this.$root.$data.erp.request.get(url, this, (data) => {
            this.customers = data.customers;
            this.products = data.products;
            if (this.method === 'Edit') {
                this.warranty = data.warranty
                this.forms.id = this.warranty.id
                this.forms.warranty_date = this.warranty.warranty_date
                this.forms.product_id = this.warranty.product.id
                this.forms.customer_id = this.warranty.customer.id
                this.forms.quantity = this.warranty.quantity
                this.forms.status = this.warranty.status
                this.forms.note = this.warranty.note
                this.forms.warranty_date=new Date(this.warranty.warranty_date)
            }
        });

    },

    methods: {
        postWarranty() {
            let requestMethod = 'post'
            let url = '/api/inventory/warranty'
            if (this.method === 'Edit') {
                requestMethod = 'patch'
                url = '/api/inventory/warranty/' + this.forms.id
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
                this.$router.push({name: "warrantyIndex"})
            }
        },
        getProducts(query) {
            if (query !== '') {
                setTimeout(() => {
                    axios.post('/api/inventory/products/getProducts', {val: query, isPurchase: true})
                        .then(response => {
                            this.products = response.data.products
                            this.loadingMessage = false;
                        })
                        .catch(error => {
                            alert(error.message)
                        })
                }, 200);
            } else {
                this.loadingMessage = true;
                this.products = []
                return null
            }
        },
    }
}