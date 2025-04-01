import SaleShowPrint from './sale-show-print.vue'
import createCustomer from '../../customer/create.vue'

const initialData = () => ({
    bank_id: null,
    bill_no: '',
    cheque_number: '',
    id: null,
    items: [],
    note: '',
    overall_discount: 0.0,
    paid: 0.0,
    payment_type: 1,
    sales_date: new Date().toISOString().split('T')[0],
    shipping_cost: 0.0,
    status: 1,
    sum_fields: [],
    customer_id: null,
    total_weight: 0.0,
    transaction_number: '',
    removed_ids: []
})
export default {
    components: {
        SaleShowPrint,
        createCustomer
    },
    props: {
        modelId: {
            type: Number,
            default: null
        },
        value: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            axiosMethod: this.$getAxiosMethod(this.value, this.modelId),
            banks: [],
            computedDate: null,
            customerModal: false,
            customers: [],
            dialog: false,
            due: 0,
            extra_weight: 0,
            forms: initialData(),
            initialPaid: 0,
            items: [],
            loading: false,
            menu1: false,
            modal: false,
            model10: [],
            part_number_dialog: false,
            payment_status: 1,
            previousDue: 0,
            print_data:null,
            product_id: '',
            products: [],
            profitPercentageField: false,
            sale_profit: false,
            sales: {status: ''},
            searchProduct: null,
            settings: [],
            tables: [],
            timeOutValue: null,
            tooltipId: "conversionTooltip",
            units: [],
            url: this.$getUrl('sales', this.value, this.modelId),
            warehouses: []
        }
    },
    watch: {
        searchProduct(val) {
            if (val) {
                this.getProducts(val)
            }
        },
        total(val) {
            this.calDue()
            if (this.axiosMethod === 'patch') {
                return
            }
            this.forms.paid = parseFloat(parseFloat(val) + parseFloat(this.previousDue)).toFixed(4)
        },
        items: {
            deep: true,
            handler(val) {
                val.map(v => {
                    if (v.location) {
                        let arr = v.location.split('-')
                        if (arr.length) v.warehouse = parseInt(arr[1])
                    }
                })
            }
        }
    },

    computed: {
        isLoaded() {
            return !_.isEmpty(this.warehouses) || !_.isEmpty(this.customers) || !_.isEmpty(this.products) || !_.isEmpty(this.units)
        },
        total() {
            let total = 0
            let shipping_cost = 0
            let discount = 0
            shipping_cost = !this.forms.shipping_cost ? 0 : this.forms.shipping_cost
            if (this.items.length > 0) {
                for (let i = 0; i < this.items.length; i++) {
                    total += this.items[i].price
                }
            }
            total = parseFloat(total) + parseFloat(shipping_cost)
            discount = this.forms.overall_discount ? total * parseFloat(this.forms.overall_discount) / 100 : 0
            total -= discount
            this.getTotalWeight()
            return total.toFixed(4)
        },
        showModal: {
            get() {
                return this.value;
            },
            set(value) {
                if (!value) this.$emit('input', value)
            }
        }

    },

    created() {
        this.getData()
    },
    methods: {
        getData() {
            this.loading = true
            axios.get(this.url)
                .then(res => {
                    let data = res.data
                    this.settings = data.settings
                    this.customers = data.customers
                    this.products = data.products
                    this.warehouses = data.warehouses
                    this.units = data.units
                    this.tables = data.tables
                    this.banks = data.banks
                    this.forms.customer_id = this.$getDefault_id(this.customers)[0].id
                    if (this.axiosMethod !== 'patch') {
                        this.getCustomerDue()
                    }
                    if (this.axiosMethod === 'patch') {
                        this.sales = data.sales
                        let part_numbers = data.sales.partnumbers
                        this.forms.sales_date = moment(this.sales.sales_date_formatted, this.$root.defaultDateFormat)
                            .format("YYYY-MM-DD")

                        this.forms.overall_discount = this.sales.overall_discount
                        this.forms.shipping_cost = this.sales.shipping_cost

                        this.initialPaid = !_.isEmpty(this.sales.payments) ? this.sales.payments[0].paid : 0
                        this.forms.paid = !_.isEmpty(this.sales.payments) ? this.sales.payments[0].paid : 0
                        this.forms.table = parseInt(this.sales.table)
                        this.forms.status = parseInt(this.sales.status)
                        this.forms.payment_status = parseInt(this.sales.payment_status)
                        this.previousDue = this.sales.previous_due
                        this.forms.customer_id = parseInt(this.sales.customer_id)

                        if (this.forms.payment_type == 2 || this.forms.payment_type == 3) {
                            this.forms.bank_id = this.purchases.payments[0].transaction.bank_id
                            this.forms.transaction_number = this.purchases.payments[0].transaction.transaction_number
                        }
                        for (let i = 0; i < this.sales.products.length; i++) {
                            let product = this.sales.products[i];
                            this.items.push({
                                product_id: product.id,
                                pname: product.name,
                                unit_price: parseFloat(product.pivot.price).toFixed(4),
                                warehouse: parseInt(product.pivot.warehouse_id),
                                unit: parseInt(product.pivot.unit_id),
                                units: product.units,
                                discount: parseFloat(product.pivot.discount).toFixed(4),
                                quantity: parseFloat(product.pivot.quantity).toFixed(4),
                                fromUnit: product.pivot.unit_id,
                                baseUnit: product.base_unit_id,
                                price: 0,
                                warehouses: product.warehouses,
                                productInStock: this.getAvailableProductCount(product.warehouses),
                                weight: product.pivot.weight,
                                weight_total: product.pivot.weight_total,
                                adjustment: product.pivot.adjustment,
                                manufacture_part_number: product.manufacture_part_number,
                                location: product.sale_location,
                                ps_id: product.pivot.ps_id,
                                part_number: this.$options.filters.sortPartNumber(
                                    product.partnumbers,
                                    product,
                                    parseInt(product.pivot.warehouse_id
                                    )
                                ),
                                selected_part_number: this.getSelectedPartNumbers(part_numbers, product)
                            })
                        }
                        this.getTotalWeight()
                        this.extra_weight = this.sales.total_weight - this.forms.total_weight
                    }
                    this.loading = false
                })
        },
        getSelectedPartNumbers(part_numbers, product) {
            let p = []
            part_numbers.forEach(pn => {
                if (pn.product_id === product.id) {
                    p.push(pn.id)
                }
            })
            return p
        },
        sortByWarehouse(part_numbers, warehouse_id) {
            return part_numbers.filter(p => {
                return p.warehouse_id === warehouse_id
            })
        },
        getTotalWeight() {
            this.forms.total_weight = 0
            this.items.forEach(item => {
                if (!item.weight_total) {
                    item.weight_total = 0
                }
                this.forms.total_weight += item.weight_total
            })
        },
        postSales() {
            this.$validator.validateAll().then(result => {
                if (result) {
                    let buttonValue = event.submitter.value
                    this.loading = true
                    this.forms.items = this.items
                    this.forms.total = this.total
                    this.forms.previous_due = this.previousDue
                    this.forms.payment_status = this.payment_status
                    this.forms.sales_date = moment(this.forms.sales_date).format("YYYY-MM-DD HH:mm:ss")
                    this.forms.extra_weight = this.extra_weight

                    axios[this.axiosMethod](this.$getUrlForm(this.url), this.forms)
                        .then(res => {
                            let data = res.data
                            this.forms.sales_date = moment(new Date()).format("YYYY-MM-DD HH:mm:ss")
                            this.forms.paid = 0
                            this.items = []
                            this.loading = false
                            swal.fire({
                                timer: 3000,
                                type: data.type,
                                text: data.message,
                                showCancelButton: false,
                                showConfirmButton: false,
                            }).then(r => {
                                if (r) {
                                    this.forms.sales_date = moment(new Date()).format("YYYY-MM-DD")
                                    //for modal
                                    if (this.value && buttonValue === 'submit-close') {
                                        this.$emit('input', false)
                                    }
                                    if (this.value && buttonValue === 'view') {
                                        this.$emit('returns-from-create', 'view', data.sale.id)
                                    }
                                    if (this.value && buttonValue === 'update') {
                                        this.$emit('input', false)
                                    }

                                    //common
                                    if (buttonValue === 'print') {
                                        this.callPrintComponent(data)
                                    }
                                    if (buttonValue === 'view') {
                                        this.$emit('view', data.sale.id)
                                    }
                                    this.resetForm()
                                    this.getCustomerDue(this.forms.customer_id)
                                    this.loading = false
                                }
                            })
                        })
                        .catch(error => {
                            this.forms.sales_date = moment(new Date()).format("YYYY-MM-DD")
                            this.loading = false
                            let err
                            let errs = error.response.data.errors
                            for (err in errs) {
                                this.errors.add({
                                    'field': err,
                                    'msg': errs[err][0],
                                })
                            }
                        })
                }
            });


        },

        callPrintComponent(data) {
            axios.get(api_base_url+'/sales/'+data.sale.id)
                .then(res=>{
                    this.print_data = res.data
                    this.print_data.totalRAmount = 0.0
                    for (let i = 0; i < this.print_data.returns.length; i++) {
                        this.print_data.totalRAmount += parseFloat(this.print_data.returns[i].amount)
                    }
                    this.$nextTick(next=>{
                        this.$htmlToPaper('myPrint')
                    })
                })
        },

        // product Sale section
        addProduct() {
            if (!this.product_id) return null;
            let data = this.mapData()
            let duplicateItem = this.$isDuplicateItemReturnIndex(data)
            if (duplicateItem > -1) {
                this.items[duplicateItem].quantity += 1
            } else {
                this.items.push(data)
            }
            this.forms.paid = 0
            this.calDue()


            // if(this.items.length > 0) {
            //     this.product_id    = null
            //     this.searchProduct = null
            this.$nextTick(() => {
                this.product_id = null
            })
            // }
        },
        resetForm() {
            const data = initialData()
            Object.keys(data).forEach(k => this.forms[k] = data[k])
            this.items = []
            this.loading = true
            this.getSum_fields()
        },
        getSum_fields() {
            let inventorySettings = this.$store.state.settings.settings.inventory
            if (inventorySettings.sale !== undefined && inventorySettings.sale.sum_fields !== undefined) {
                if (!_.isEmpty(inventorySettings.sale.sum_fields)) {
                    this.forms.sum_fields = inventorySettings.sale.sum_fields
                }
            } else {
                this.forms.sum_fields = []
            }
        },
        mapData() {
            let product = this.getProduct(this.product_id)
            return {
                product_id: this.product_id,
                pname: product.name,
                unit_price: parseFloat(product.price).toFixed(4),
                price: 0,
                warehouses: product.warehouses,
                warehouse: this.select_default_warehouse(product.warehouses),
                productInStock: this.getAvailableProductCount(product.warehouses),
                fromUnit: product.base_unit_id,
                baseUnit: product.base_unit_id,
                units: product.units,
                unit: this.$getPrimary_id(product.units)[0].id,
                weight: this.findWeight(this.product_id),
                manufacture_part_number: product.manufacture_part_number,
                part_number: product.partnumbers,
                location: '',
                quantity: 1
            }
        },
        itemTotalWeight(index) {
            this.items[index].weight_total = this.items[index].quantity
                ? this.items[index].weight * this.items[index].quantity
                : 0

            return this.items[index].weight_total
        },
        checkSelected(part_numbers, selected, quantity) {
            part_numbers.forEach(p => {
                p.disabled = null
            })
            if (selected) {
                if (selected.length === Number(quantity)) {
                    part_numbers.forEach(p => {
                        if (!selected.includes(p.id)) {
                            p.disabled = true
                        }
                    })
                }
            }
        },
        findWeight(product_id) {
            let product = this.getProduct(product_id)
            let units = product.units
            if (units.length > 0) {
                let weight = units.find((unit) => {
                    return unit.id === product.base_unit_id
                }).pivot.weight
                return weight ? weight : 0;
            } else {
                return 0;
            }
        },
        select_default_warehouse(warehouses) {
            console.log(warehouses)
            let datas = null
            warehouses.filter(data => {
                if (data.is_default) {
                    datas = data.id
                }
            })
            if (!datas) {
                return warehouses[0].id
            } else {
                return datas
            }
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
            let totalPrice = 0
            if (item.unit_price && item.quantity) {
                totalPrice = Number(item.unit_price) * Number(item.quantity)
            }
            if (totalPrice && item.discount) {
                totalPrice -= Number(item.discount)
            }
            if (totalPrice && item.adjustment) {
                totalPrice += Number(item.adjustment)
            }
            item.price = Number(parseFloat(totalPrice).toFixed(2))
            return item.price
        },
        calDue() {
            this.$nextTick(() => {
                this.due = ((parseFloat(this.total) + parseFloat(this.previousDue)) - parseFloat(this.forms.paid ? this.forms.paid : 0)).toFixed(4)
                if (this.due <= 0) {
                    this.payment_status = 1 //paid
                }
                if (this.due > 0) {
                    this.payment_status = 2 // partial
                }
                if (this.due == (parseFloat(this.total) + parseFloat(this.previousDue)).toFixed(4) && this.due != 0) {
                    this.payment_status = 3 //due
                }
            })


        },
        getPrice(unit, index) {
            let fromUnit = this.items[index].fromUnit
            this.items[index].fromUnit = unit
            this.items[index].weight = _.find(this.items[index].units, {id: unit}).pivot.weight
            if (fromUnit === unit) {
                return
            }

            let url = '/api/inventory/unitconversions/' + unit + '/' + fromUnit + '/1'
            axios.post(url)
                .then(response => {
                    let conversion = response.data.conversion
                    this.items[index].unit_price = parseFloat(response.data.quantity * this.items[index].unit_price).toFixed(4)
                })
        },
        getAvailableProductCount(warehouses) {
            let quantity = 0
            for (var i = 0; i < warehouses.length; i++) {
                let warehouse = warehouses[i]
                quantity += parseFloat(warehouse.pivot.quantity)

            }
            return quantity
        },
        updateCustomerList(val) {
            this.customers.push(val)
            this.forms.customer_id = val.id
            this.$nextTick(() => {
                this.dialog = false
            })
        },
        getClickedProduct(val) {
            if (val) {
                this.product_id = val
                this.addProduct()
            }
        },
        getProducts: _.debounce(function (query) {
            if (query !== '') {
                axios.get('/api/inventory/products/getProducts', {
                    params: {
                        val: query,
                        isPurchase: true,
                        with_part_number: true
                    }
                })
                    .then(response => {
                        this.products = response.data.products
                        // console.log( response.data.products, query )
                        // if(this.products.length === 1) {
                        //     this.product_id = this.products[0].id
                        // this.addProduct()
                        // }
                        this.loading = false
                    })
                    .catch(error => {
                        alert(error.message)
                    })
            } else {
                this.loading = true;
                this.products = []
                return null
            }
        }, 1000),

        getInputField(event) {
            this.profitPercentageField = event.target.checked;
        },
        getCustomerDue() {
            axios.get('/api/inventory/customer/get-due/' + this.forms.customer_id)
                .then(response => {
                    this.previousDue = response.data.due ? response.data.due : 0
                    this.calDue()
                })
        },
    }
}

