export default {
    data() {
        return {
            forms: this.$root.$data.forms,
            productDamages: [],
            warehouses: [],
            units: [],
            products: [],
            method: '',
            button: {
                text: 'Submit',
                cancel: 'Reset'
            },
            title: {
                text: 'ADD'
            },
            placeholder: "Select product"
        }
    },

    beforeCreate() {
        this.$root.$data.forms.fieldReset();
    },

    watch: {
        '$route.fullPath'(val) {
            //reset data
            this.forms.reset();
            this.productDamages = []
            this.method = 'Create'
            this.button.text = "Submit"
            this.button.cancel = 'Reset'
            this.title.text = "ADD"
        }

    },


    computed: {
        isLoaded() {
            if (this.method === 'Edit') {

                return !_.isEmpty(this.productDamages)

            } else {
                return true
            }
        }
    },

    created() {
        this.method = this.$root.getMethodAction();
        let url = '/api/inventory/productdamages/create';
        if (this.method === 'Edit') {
            url = '/api/inventory/productdamages/' + this.$route.params.id + '/edit'
            this.button.text = "Update"
            this.button.cancel = "Cancel"
            this.title.text = "UPDATE"
        }
        this.$root.$data.erp.request.get(url, this, (data) => {
            this.warehouses = data.warehouses;
            if (this.method === 'Edit') {
                this.productDamages = data.productdamages
                this.products = data.products
                this.units = data.units
                this.forms.id = this.productDamages.id
                this.forms.product_id = parseInt(this.productDamages.product_id)
                this.forms.warehouse_id = parseInt(this.productDamages.warehouse_id)
                this.forms.quantity = this.productDamages.quantity
                this.forms.unit_id = parseInt(this.productDamages.unit_id)
                this.forms.sale_value = this.productDamages.sale_value
            }
        });
    },

    methods: {
        postProductDamage() {
            let requestMethod = 'post'
            let url = '/api/inventory/productdamages'
            if (this.method === 'Edit') {
                requestMethod = 'patch'
                url = '/api/inventory/productdamages/' + this.forms.id
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
            if (this.method === "Edit") {
                this.$router.push({name: "productdamagesIndex"})
            }
        },
        getProducts() {
            this.placeholder = "Loading...";
            axios.post('/api/inventory/products/getProducts', {
                warehouseId: this.forms.warehouse_id,
                filterWarehouse: true
            })
                .then(response => {
                    this.products = response.data.products
                    this.placeholder = "Select product";
                })
                .catch(error => {
                    alert(error.message)
                })
        },
        getUnits() {
            this.placeholder = "Loading...";
            axios.post('/api/inventory/units/getUnits', {
                productId: this.forms.product_id
            })
                .then(response => {
                    this.units = response.data.units
                    this.placeholder = "Select Unit";
                })
                .catch(error => {
                    alert(error.message)
                })
        }
    }
}