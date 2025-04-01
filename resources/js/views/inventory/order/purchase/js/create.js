export default {
    data() {
        return {
            loadingMessage: false,
            productName: null,
            model10: [],
            purchases: [],
            warehouses: [],
            suppliers: [],
            units: [],
            unitconversions: [],
            products: [],
            product_id: '',
            forms: this.$root.$data.forms,
            items: [],
            due: 0,
            payment_status: 1,
            payment_type: 1,
            tooltipId: "conversionTooltip",
            button: {
                text: 'Submit',

            },
            title: {
                text: 'ADD'
            }

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
            this.purchases = []
            this.product_id = ''
            this.items = []
            this.due = 0
            this.payment_status = 1
            this.payment_type = 1
        },

        total(val) {
            if (this.method === 'Edit') {
                this.calDue()
                return
            }
            this.forms.paid = val
        }
    },

    computed: {
        isLoaded() {
            return !_.isEmpty(this.warehouses) || !_.isEmpty(this.suppliers) || !_.isEmpty(this.products) || !_.isEmpty(this.units)
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
        this.purchases.order_date = new Date()
        this.method = this.$root.getMethodAction();
        let url = "/api/inventory/purchases/create"
        if (this.method === "Edit") {
            this.button.text = "Update"
            this.title.text = "UPDATE"
            url = "/api/inventory/purchases/" + this.$route.params.id + "/edit"
        }

        this.$root.$data.erp.request.get(url, this, (data) => {
            this.suppliers = data.suppliers
            this.products = data.products
            this.warehouses = data.warehouses
            this.units = data.units
            this.unitconversions = data.unitconversions

            if (this.method === "Edit") {
                this.purchases = data.purchases
                this.forms.order_date = this.purchases.order_date
                this.$root.$set(this.forms, 'overall_discount', this.purchases.overall_discount);
                this.$root.$set(this.forms, 'shipping_cost', this.purchases.shipping_cost);
                this.forms.paid = this.purchases.payments[0].paid
                this.forms.supplier_id = parseInt(this.purchases.supplier_id)
                this.forms.status = this.purchases.status
                this.forms.note = this.purchases.note
                for (let i = 0; i < this.purchases.products.length; i++) {
                    let product = this.purchases.products[i];
                    this.items.push({
                        product_id: product.id,
                        pname: product.name,
                        unit_price: product.pivot.price,
                        warehouse: parseInt(product.pivot.warehouse_id),
                        unit: parseInt(product.pivot.unit_id),
                        units: product.units,
                        discount: product.pivot.discount,
                        quantity: product.pivot.purchase_quantity,
                        fromUnit: product.pivot.unit_id,
                        baseUnit: product.base_unit_id,
                        price: 0
                    });
                }
            }
        });
        this.forms.order_date = new Date()
        this.forms.payment_status = 3
    },
    methods: {
        postPurchase() {
            this.forms.items = this.items
            this.forms.total = this.total
            this.forms.payment_status = this.payment_status
            this.forms.payment_type = this.payment_type

            let requestMethod = 'post'
            let url = '/api/inventory/orders/Purchase'
            if (this.method === 'Edit') {
                requestMethod = 'patch'
                url = '/api/inventory/orders/purchases/' + this.purchases.id
            }

            this.forms.submit(requestMethod, url, false, this.$root)
                .then(data => {
                    if (data.type === 'success') {
                        this.items = []
                    }
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

        getProducts(query) {
            if (query !== '') {
                setTimeout(() => {
                    axios.post('/api/inventory/products/getProducts', {val: query, isPurchase: true})
                        .then(response => {
                            this.products = response.data.products
                            console.log(this.products);
                            this.loadingMessage = false;
                        })
                        .catch(error => {
                            alert(error.message)
                        })
                }, 200);
            } else {
                this.loadingMessage = true;
                this.products = []
                console.log(this.products);
                return null
            }
        },

        // product Purchase section
        addProduct() {
            if (!this.product_id) return null;
            // if (this.uniqueItem(this.product_id)) return null;
            this.items.push({
                product_id: this.product_id,
                pname: this.getProduct(this.product_id).name,
                unit_price: this.getProduct(this.product_id).buying_price,
                price: 0,
                fromUnit: this.getProduct(this.product_id).base_unit_id,
                baseUnit: parseFloat(this.getProduct(this.product_id).base_unit_id),
                units: this.getProduct(this.product_id).units,
                warehouse: this.$root.getDefault_id(this.warehouses),
                unit: this.$root.getPrimary_id(this.getProduct(this.product_id).units),
            });
        },
        uniqueItem(id) {
            return this.items.find((item) => {
                return item.product_id === id
            })
        },
        getPurchasePivot(product, id) {
            return product.purchases.find((purchase) => {
                return purchase.id === id
            })
        },
        getProduct(id) {
            return this.products.find((product) => {
                return product.id === this.product_id
            })
        },
        removeProduct(index) {
            this.items.splice(index, 1);
        },
        getTotalPrice(item) {
            let totalPrice = 0
            let unit_price = parseFloat(item.unit_price).toFixed(4);
            if (isNaN(item.quantity)) {
                return totalPrice
                item.price = totalPrice
            } else {
                if (isNaN(item.discount)) {
                    totalPrice = (item.quantity * unit_price)
                } else {
                    totalPrice = (item.quantity * unit_price) - item.discount
                }
                item.price = totalPrice
                return totalPrice
            }
        },
        calDue() {
            this.due = this.total - this.forms.paid

            if (this.due == 0) {
                this.payment_status = 1 //paid
            }
            if (this.due < this.total && this.due > 0) {
                this.payment_status = 2 //partial
            }
            if (this.due == this.total) {
                this.payment_status = 3 //due
            }
        },
        getPrice(unit, index) {
            let fromUnit = this.items[index].fromUnit
            this.items[index].fromUnit = unit
            if (fromUnit === unit) {
                return
            }

            let url = '/api/inventory/unitconversions/' + unit + '/' + fromUnit + '/1'
            axios.post(url, {isPurchase: true, productId: this.items[index].product_id})
                .then(response => {
                    let conversion = response.data.conversion
                    this.items[index].unit_price = (response.data.quantity * this.items[index].unit_price)
                })
        },
        onCancel() {

            this.$router.push({name: "purchaseIndex"})

        }
    }
}