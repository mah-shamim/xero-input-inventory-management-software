import CustomerCreate from '../../customer/create';

export default {

    components: {
        'customer-create': CustomerCreate
    },

    data() {
        return {
            orders: {
                status:''
            },
            warehouses: [],
            customers: [],
            units: [],
            products: [],
            product_id: '',
            forms: this.$root.$data.forms,
            items: [],
            tooltipId: "conversionTooltip",
            button: {
                text: 'Submit'
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
            this.method = 'Create'
            this.button.text = "Submit"
            this.title.text = "ADD"
            this.forms.reset()
            this.orders = []
            this.product_id = ''
            this.items = []
        }/*,
        total(val) {
            if (this.method === 'Edit') {
                this.calDue()
                return
            }
            this.forms.paid = val
        }*/
    },


    computed: {
        isLoaded() {
            return !_.isEmpty(this.warehouses) || !_.isEmpty(this.customers) || !_.isEmpty(this.products) || !_.isEmpty(this.units)
        },

        total() {
            let total = 0
            let shipping_cost = 0
            let discount = 0
            shipping_cost = !this.forms.shipping_cost ? 0 : this.forms.shipping_cost;

            if (this.items.length > 0) {
                for (let i = 0; i < this.items.length; i++) {
                    total += this.items[i].price
                }
            }
            total = parseFloat(total) + parseFloat(shipping_cost)

            discount = this.forms.overall_discount ? total * parseFloat(this.forms.overall_discount) / 100 : 0

            total -= discount

            return total.toFixed(4)
        }

    },

    created() {
        this.method = this.$root.getMethodAction();
        let url = "/api/inventory/orders/create"
        if (this.method === "Edit") {
            url = "/api/inventory/orders/" + this.$route.params.id + "/edit"
        }

        this.$root.$data.erp.request.get(url, this, (data) => {
            this.customers = data.customers
            this.products = data.products
            this.warehouses = data.warehouses
            this.units = data.units
            this.forms.order_date = new Date()
            if (this.method === 'Edit') {
                this.orders = data.orders

                this.button.text = "Update"
                this.title.text = "Update"
                this.forms.order_date = this.orders.order_date
                this.$root.$set(this.forms, 'overall_discount', this.orders.overall_discount);
                this.$root.$set(this.forms, 'shipping_cost', this.orders.shipping_cost);
                this.forms.paid = this.orders.payments[0].paid
                this.forms.status = this.orders.status
                this.forms.customer_id = parseInt(this.orders.customer_id)
                for (let i = 0; i < this.orders.products.length; i++) {
                    let product = this.orders.products[i];
                    this.items.push({
                        product_id: product.id,
                        pname: product.name,
                        unit_price: product.pivot.price,
                        warehouse: parseInt(product.pivot.warehouse_id),
                        unit: parseInt(product.pivot.unit_id),
                        units: product.units,
                        discount: product.pivot.discount,
                        quantity: product.pivot.quantity,
                        fromUnit: product.pivot.unit_id,
                        baseUnit: product.base_unit_id,
                        price: 0,
                        warehouses: product.warehouses,
                        productInStock: this.getAvailableProductCount(product.warehouses)
                    });

                }
            }
        });


    },
    methods: {
        postOrders() {
            this.forms.items = this.items
            this.forms.total = this.total
            let requestMethod = 'post'
            let url = '/api/inventory/orders'
            if (this.method === 'Edit') {
                requestMethod = 'patch'
                url = '/api/inventory/orders/' + this.orders.id
            }
            this.forms.submit(requestMethod, url, false, this.$root)
                .then(data => {
                    this.items = [],
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

        // product Sale section
        addProduct() {
            if (!this.product_id) return null;
            this.items.push({
                product_id: this.product_id,
                pname: this.getProduct(this.product_id).name,
                unit_price: this.getProduct(this.product_id).price,
                price: 0,
                warehouses: this.getProduct(this.product_id).warehouses,
                productInStock: this.getAvailableProductCount(this.getProduct(this.product_id).warehouses),
                fromUnit: this.getProduct(this.product_id).base_unit_id,
                baseUnit: this.getProduct(this.product_id).base_unit_id,
                units: this.getProduct(this.product_id).units
            });
        },
        uniqueItem(id) {
            return this.items.find((item) => {
                return item.product_id === id
            })
        },
        getProduct(id) {
            return this.products.find((product) => {
                return product.id === this.product_id
            })
        },
        getProductById(id) {
            return this.products.find((product) => {
                return product.id === id
            })
        },
        removeProduct(index) {
            this.items.splice(index, 1);
        },
        getTotalPrice(item) {
            console.log("test");
            let totalPrice = 0
            if (isNaN(item.quantity)) {
                return 'total: ' + totalPrice
                item.price = totalPrice
            } else {
                if (isNaN(item.discount)) {
                    totalPrice = (item.quantity * item.unit_price)
                } else {
                    totalPrice = (item.quantity * item.unit_price) - item.discount
                }
                item.price = totalPrice
                return 'total: ' + totalPrice
            }
        },
        getPrice(unit, index) {
            let fromUnit = this.items[index].fromUnit
            this.items[index].fromUnit = unit
            if (fromUnit === unit) {
                return
            }

            let url = '/api/inventory/unitconversions/' + unit + '/' + fromUnit + '/1'
            axios.post(url)
                .then(response => {
                    let conversion = response.data.conversion
                    this.items[index].unit_price = (response.data.quantity * this.items[index].unit_price)

                })
        },
        getAvailableProductCount(warehouses) {
            let quantity = 0
            for (var i = 0; i < warehouses.length; i++) {
                let warehouse = warehouses[i]
                quantity += warehouse.pivot.quantity

            }
            return parseFloat(quantity).toFixed(4)
        },
        getNewCustomer(customerDetails) {
            this.customers.push(customerDetails);
            console.log(customerDetails.id);
            this.forms.customer_id = customerDetails.id;
        },
        onCancel() {
            this.$router.push({name: "salesIndex"})

        },

        getProducts(query) {
            if (query !== '') {
                setTimeout(() => {
                    axios.post('/api/inventory/products/getProducts', {val: query, isSale: true})
                        .then(response => {
                            this.products = response.data.products
                            this.loading = false;
                        })
                        .catch(error => {
                            alert(error.message)
                        })
                }, 200);
            } else {
                this.loading = true;
                this.products = []
                console.log(this.products);
                return null
            }
        }
    }
}