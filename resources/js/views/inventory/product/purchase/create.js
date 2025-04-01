import createSupplier from '../../supplier/create.vue'
import PurchaseShowPrint from './purchase-show-print.vue'
const initialData = () => ({
    bank_id: null,
    bill_no: '',
    cheque_number: '',
    id: null,
    items: [],
    note: '',
    overall_discount: 0.0,
    paid: 0.0,
    payment_type: 3,
    purchase_date: new Date().toISOString().split('T')[0],
    shipping_cost: 0.0,
    status: 1,
    sum_fields: [],
    supplier_id: null,
    total_weight: 0.0,
    transaction_number: '',
    removed_ids: []
})
export default {
    components: {
        createSupplier,
        PurchaseShowPrint,
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
            due: 0,
            banks: [],
            modal: false,
            menu1: false,
            items: [],
            url: this.$getUrl('purchases', this.value, this.modelId),
            axiosMethod: this.$getAxiosMethod(this.value, this.modelId),
            forms: initialData(),
            units: [],
            model10: [],
            loading: false,
            products: [],
            suppliers: [],
            tooltipId: "conversionTooltip",
            purchases: [],
            warehouses: [],
            product_id: '',
            extra_weight: 0,
            payment_type: 3,
            computedDate: null,
            searchProduct: null,
            loadingMessage: false,
            payment_status: 1,
            productLoading: false,
            supplier_dialog: false,
            unitconversions: [],
            part_number_dialog: false,
            print_Data: {
                supplier: {},
                payments: [],
                returns: []
            },
            print_totalRAmount: 0
        }
    },
    watch: {
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
        },
        searchProduct(val) {
            this.getProducts(val)
        },
        '$route.fullPath'(val) {
            //reset data
            this.forms.reset()
            this.due = 0
            this.items = []
            this.purchases = []
            this.product_id = ''
            this.payment_type = 3
            this.payment_status = 1
        },

        total(val) {
            if (this.axiosMethod === 'patch') {
                this.calDue()
                return
            }
            // this.forms.paid = val
        },
    },

    computed: {
        isLoaded() {
            return !_.isEmpty(this.warehouses) || !_.isEmpty(this.suppliers) || !_.isEmpty(this.products) || !_.isEmpty(this.units)
        },
        total() {
            let total = 0
            let discount = 0
            let shipping_cost = 0

            shipping_cost = !this.forms.shipping_cost ? 0 : this.forms.shipping_cost;

            if (this.items.length > 0) {
                for (let i = 0; i < this.items.length; i++) {
                    total += this.items[i].price
                }
            }

            total = parseFloat(total) + parseFloat(shipping_cost)

            discount = this.forms.overall_discount ? total * parseFloat(this.forms.overall_discount) / 100 : 0

            total -= discount

            if (this.forms.sum_fields !== undefined) {
                total += _.sumBy(this.forms.sum_fields, item => {
                    return item.active ? Number(item.value) : Number(0);
                })
            }

            this.getTotalWeight()
            return total.toFixed(3)
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
            this.payment_type = this.$root.purchaseDefaultPaymentMethod
            this.loading = true
            this.getSum_fields()
            axios.get(this.url)
                .then(res => {
                    let data = res.data
                    this.suppliers = data.suppliers
                    this.products = data.products
                    this.banks = data.banks
                    this.warehouses = data.warehouses
                    this.units = data.units
                    this.unitconversions = data.unitconversions
                    if (this.axiosMethod === 'patch') {
                        let part_numbers = data.purchases.partnumbers
                        this.purchases = data.purchases
                        this.forms.purchase_date = moment(this.purchases.purchase_date_formatted, this.$root.defaultDateFormat).format("YYYY-MM-DD")
                        this.forms.overall_discount = this.purchases.overall_discount
                        this.forms.shipping_cost = this.purchases.shipping_cost
                        this.forms.supplier_id = parseInt(this.purchases.supplier_id)
                        this.forms.bill_no = this.purchases.bill_no
                        this.forms.status = this.purchases.status
                        this.forms.note = this.purchases.note
                        this.forms.sum_fields = this.purchases.sum_fields

                        if (this.purchases.payments !== undefined && this.purchases.payments.length > 0) {
                            this.forms.paid = this.purchases.payments[0].paid
                            this.payment_type = this.purchases.payments[0].payment_type
                            if (this.payment_type == 2 || this.payment_type == 3) {
                                this.forms.bank_id = this.purchases.payments[0].transaction.bank_id
                                this.forms.transaction_number = this.purchases.payments[0].transaction.transaction_number
                            }
                        } else {
                            this.forms.paid = 0
                            this.payment_type = this.$root.purchaseDefaultPaymentMethod
                        }

                        for (let i = 0; i < this.purchases.products.length; i++) {
                            let product = this.purchases.products[i];
                            // console.log(parseFloat(product.pivot.quantity).toFixed(3))
                            this.items.push({
                                quantity: Number(product.pivot.quantity).toFixed(3),
                                product_id: product.id,
                                pname: product.name,
                                unit_price: parseFloat(product.pivot.price).toFixed(2),
                                warehouse: parseInt(product.pivot.warehouse_id),
                                unit: parseInt(product.pivot.unit_id),
                                units: product.units,
                                discount: product.pivot.discount,
                                fromUnit: product.pivot.unit_id,
                                baseUnit: product.base_unit_id,
                                price: 0,
                                weight: product.pivot.weight,
                                weight_total: product.pivot.weight_total,
                                adjustment: product.pivot.adjustment,
                                manufacture_part_number: product.manufacture_part_number,
                                location: product.purchased_location,
                                pp_id: product.pivot.pp_id,
                                part_number: this.$options.filters.sortPartNumber(
                                    part_numbers,
                                    product,
                                    parseInt(product.pivot.warehouse_id)
                                )
                            })
                        }
                        this.getTotalWeight()
                        this.extra_weight = this.purchases.total_weight - this.forms.total_weight
                    }
                    this.loading = false
                })

        },
        getNewSupplier(val) {
            this.suppliers.push(val)
            this.forms.supplier_id = val.id
            this.supplier_dialog = false
        },
        getTotalWeight() {
            this.forms.total_weight = 0
            this.items.forEach(item => {
                if (!item.weight_total) {
                    item.weight_total = 0
                }
                this.forms.total_weight += Number(parseFloat(item.weight_total).toFixed(3))
            })
        },
        getSum_fields() {
            let inventorySettings = this.$store.state.settings.settings.inventory
            if (inventorySettings.purchase !== undefined && inventorySettings.purchase.sum_fields !== undefined) {
                if (!_.isEmpty(inventorySettings.purchase.sum_fields)) {
                    this.forms.sum_fields = inventorySettings.purchase.sum_fields
                }
            } else {
                this.forms.sum_fields = []
            }
        },
        postPurchase() {
            this.$validator.validateAll().then(result => {
                if (result) {
                    let buttonValue = event.submitter.value
                    // this.loading=true
                    this.forms.items = this.items
                    this.forms.total = this.total
                    this.forms.payment_status = this.payment_status
                    this.forms.payment_type = this.payment_type
                    this.forms.purchase_date_formatted = moment(this.forms.purchase_date).format("YYYY-MM-DD")
                    // this.forms.purchase_date_formatted = moment(this.forms.purchase_date.format("YYYY-MM-DD HH:mm:ss"))
                    // console.log(this.forms.purchase_date_formatted)
                    this.forms.extra_weight = this.extra_weight
                    // console.log(this.forms.payment_type)
                    if ((this.forms.payment_type == 3 && this.forms.bank_id) && this.axiosMethod !== 'patch') {
                        let bank = _.find(this.banks, {
                            id: this.forms.bank_id
                        })
                        if (this.forms.paid >= bank.running_balance) {
                            let confirmation = confirm(bank.name + ' has low balance. Do you still want to continue?')
                            if (!confirmation) {
                                return null;
                            }
                        }
                    }

                    axios[this.axiosMethod](this.$getUrlForm(this.url), this.forms)
                        .then(res => {
                            let data = res.data
                            swal({
                                type: data.type,
                                timer: 2000,
                                text: data.message,
                                showCancelButton: false,
                                showConfirmButton: false
                            }).then(r => {
                                if (r) {
                                    this.forms.purchase_date = moment(new Date()).format("YYYY-MM-DD")
                                    this.loading = false
                                    //for modal
                                    if (this.value && buttonValue === 'submit-close') {
                                        this.$emit('input', false)
                                    }
                                    if (this.value && buttonValue === 'view') {
                                        this.$emit('returns-from-create', 'view', data.purchase.id)
                                    }
                                    if (this.value && buttonValue === 'update') {
                                        this.$emit('input', false)
                                    }

                                    //common
                                    if (buttonValue === 'print') {
                                        this.call_print_component(data)
                                    }
                                    if (buttonValue === 'view') {
                                        this.$emit('view', data.purchase.id)
                                    }
                                    this.resetForm()
                                    this.loading = false
                                }
                            }).catch(swal.noop)

                        })
                        .catch(error => {
                            this.forms.purchase_date = moment(new Date()).format("YYYY-MM-DD")
                            this.loading = false
                            let err
                            let errs = error.response.data.errors
                            for (err in errs) {
                                this.errors.add({
                                    'field': err,
                                    'msg': errs[err][0],
                                })
                            }
                        });
                }
            });
        },
        resetForm() {
            const data = initialData()
            Object.keys(data).forEach(k => this.forms[k] = data[k])
            this.items = []
            this.loading = true
            this.getSum_fields()
        },
        call_print_component(data) {
            axios.get('/api/inventory/purchases/' + data.purchase.id)
                .then(res => {
                    this.print_Data = res.data.purchase
                    this.print_totalRAmount = 0
                    if (this.print_Data.returns.length > 0) {
                        for (let i = 0; i < this.print_Data.returns.length; i++) {
                            this.print_totalRAmount += parseFloat(this.print_Data.returns[i].amount)
                        }
                    }
                    this.$nextTick(next => {
                        this.$htmlToPaper('myPrint')
                    })
                })
        },
        getProducts: _.debounce(function (query) {
            if (query !== '') {
                axios.get('/api/inventory/products/getProducts', {
                        params:{
                            val: query,
                            isPurchase: true
                        }
                    })
                    .then(response => {
                        this.products = response.data.products
                        this.loadingMessage = false;
                    })
                    .catch(error => {
                        alert(error.message)
                    })
            } else {
                this.loadingMessage = true
                this.products = []
                return null
            }
        }, 1000),
        findWeight(product_id) {
            let product = this.getProduct(product_id)
            let units = product.units
            if (units.length > 0) {
                let weight = units.find((unit) => {
                    return unit.id === product.base_unit_id
                }).pivot.weight
                return weight ?? 0;
            } else {
                return 0;
            }
        },
        addProduct() {
            if (!this.product_id) {
                return null
            }
            let data = this.mapData();
            let duplicateItem = this.$isDuplicateItemReturnIndex(data)
            if (duplicateItem > -1) {
                this.items[duplicateItem].quantity += 1
            } else {
                this.items.push(data)
            }

            if (this.items.length > 0) {
                this.$nextTick(() => {
                    this.product_id = null
                })
            }
        },
        mapData() {
            let product = this.getProduct(this.product_id)
            return {
                product_id: product.id,
                pname: product.name,
                unit_price: parseFloat(product.buying_price).toFixed(2),
                price: 0,
                fromUnit: product.base_unit_id,
                baseUnit: parseFloat(product.base_unit_id),
                units: product.units,
                warehouse: this.$getDefault_id(this.warehouses)[0].id,
                unit: this.$getPrimary_id(product.units)[0].id,
                weight: this.findWeight(this.product_id),
                manufacture_part_number: product.manufacture_part_number,
                part_number: [],
                quantity: 1,
                location: ''
            };
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
            this.forms.removed_ids.push(this.items[index]['product_id'])
            this.items.splice(index, 1);
        },
        itemTotalWeight(index) {
            this.items[index].weight_total = this.items[index].quantity
                ? this.items[index].weight * this.items[index].quantity
                : 0

            return Number(parseFloat(this.items[index].weight_total).toFixed(3))
        },
        getTotalPrice(item) {
            let totalPrice = 0

            if (item.unit_price && item.quantity) {
                totalPrice = Number(item.unit_price) * Number(item.quantity)
            }
            if (totalPrice && item.discount) {
                totalPrice -= item.discount
            }
            if (totalPrice && item.adjustment) {
                totalPrice += Number(item.adjustment)
            }
            item.price = Number(parseFloat(totalPrice).toFixed(2))
            return item.price

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
            this.items[index].weight = _.find(this.items[index].units, {
                id: unit
            }).pivot.weight
            if (fromUnit === unit) {
                return
            }

            let url = '/api/inventory/unitconversions/' + unit + '/' + fromUnit + '/1'
            axios.post(url, {
                isPurchase: true,
                productId: this.items[index].product_id
            })
                .then(response => {
                    let conversion = response.data.conversion
                    this.items[index].unit_price = parseFloat(response.data.quantity * this.items[index].unit_price).toFixed(2)
                })
        },
        onCancel() {
            if (!this.value) {
                this.$router.push({
                    name: "purchase.index"
                })
            } else {
                this.forms.reset();
                this.items = []
                this.forms.items = []
            }
        },
    }
}