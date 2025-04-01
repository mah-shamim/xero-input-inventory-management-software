import _ from "lodash";
import CreateSale from './create.vue'
import ShowPurchase from "./show.vue";

export default {
    components: {
        ShowPurchase,
        CreateSale
    },
    data() {
        return {
            closeOnContentClick: true,
            columnMenu: false,
            createOrUpdateDialog: false,
            dense: false,
            edit_id: null,
            show_id: null,
            exportDialog: false,
            headers: [
                {text: 'sales date', value: 'sales_date', align: ''},
                {text: 'ref', value: 'ref', align: ''},
                {text: 'product count', value: 'products_count', sortable: true, align: 'd-none'},
                {text: 'payment count', value: 'payments_count', sortable: true, align: 'd-none'},
                {text: 'status', value: 'status', align: ''},
                {text: 'customer-name-id', value: 'customer_name_id', sortable: false, align: ''},
                {text: 'biller', value: 'biller', sortable: false, align: ''},
                {text: 'total', value: 'total', sortable: true, align: ''},
                {text: 'total paid', value: 'total_paid', sortable: true, align: ''},
                {text: 'due', value: 'due', sortable: true, align: ''},
                {text: 'total weight', value: 'total_weight', sortable: true, align: ''},
                // {text: 'created at', value: 'created_at', align: ''},
                {text: 'action', value: 'action', sortable: false, align: ''},
            ],
            isShowFilter: true,
            items: {},
            loading: false,
            options: {
                sortBy: ['created_at'],
                sortDesc: [true],
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            paymentHeaders: [
                {text: 'amount', value: 'paid'},
                {text: 'date', value: 'date'},
                {text: 'action', value: 'action', action: false},
            ],
            payment_crud_dialog: false,
            payment_crud_method: 'create',
            payment_crud_model_id: null,
            payment_crud_payment_id: null,
            payment_list_dialog: false,
            payments: []
        }
    },
    watch: {
        options: {
            deep: true,
            handler() {
                this.loading = true
                this.getData()
            }
        },
        createOrUpdateDialog(val) {
            if (!val) {
                this.getData()
                this.edit_id = null
            }
        }
    },
    methods: {
        uploadFile() {
            let importHeaders = [
                'ref', //0
                'customer id', //1
                'total price', //2
                'shipping cost', //3
                'overall discount', //4
                'sales date', //5
                'total weight', //6
                'product code', //7
                'product name', //8
                'warehouse code', //9
                'warehouse name', //10
                'unit id', //11
                'unit name', //12
                'quantity', //13
                'unit price', //14
                'product discount', //15
                'product total price(calculated)', //16
                'product weight total(calculated)', //17
                'adjustment', //18
            ]
            let formData = new FormData()
            let file = this.$refs.inputUpload.files[0]
            formData.append('file', file)
            axios.post('/api/inventory/sale-import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(res => {
                    swal.fire({
                        icon: 'success',
                        text: 'file has been uploaded successfully, please update payment'
                    }).then((result) => {
                        this.getData()
                    })
                })
                .catch(error => {
                    let errorArr = []
                    if (error.response.status === 422) {
                        // console.log(error.response.data.errors, error.response.status)
                        Object.keys(error.response.data.errors).forEach((e, i) => {
                            let row = Number(e.split('.')[0]) + 1
                            let column = Number(e.split('.')[1])
                            let listNumber = i + 1
                            let html = '<p>' + listNumber + '. Incorrect entry at row: ' + row + '; column: ' + importHeaders[column] + '.</p>'
                            errorArr.push(html)
                        })
                        // console.log(errorArr, errorArr.toString())
                        swal.fire({
                            icon: 'error',
                            html: errorArr.join("")
                        })
                    } else {
                        swal.fire({
                            icon: 'error',
                            html: 'status ' + error.response.status + '.<br>' + error.response.statusText,
                        })
                    }
                })
        },
        closeDialogExport(val) {
            if (val === 200) {
                this.exportDialog = false
            }
        },
        getListOfPayments(id) {
            this.payments = []
            axios
                .get('/api/payments/received', {
                    params: {id: id}
                })
                .then(res => {
                    this.payments = res.data
                    if (this.payments.length > 0) {
                        this.payment_list_dialog = true
                    }
                })
        },
        resetPaymentComponent() {
            this.payment_crud_method = 'create'
            this.payment_crud_model_id = null
            this.payment_crud_payment_id = null
        },
        createPayment() {
            this.resetPaymentComponent()
            this.payment_crud_dialog = true
        },
        createPaymentById(id) {
            this.method = 'create'
            this.payment_crud_model_id = id
            this.payment_crud_dialog = true
        },
        editPayment(id) {
            this.payment_crud_payment_id = id
            this.payment_crud_method = 'edit'
            this.payment_crud_dialog = true
            this.payment_list_dialog = false
        },
        paymentSuccess(val) {
            if (val) {
                this.getData()
                this.payment_crud_dialog = false
            }
        },
        deleteItem(id) {
            this.loading = true
            this.$deleteWithConfirmation({
                text: 'Are you sure you want delete this sale?',
                url: '/api/inventory/sales/' + id
            })
                .then(data => {
                    this.getData()
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        getData: _.debounce(function () {
            this.loading = true
            axios.get('/api/inventory/sales', {
                params: this.options
            })
                .then(res => {
                    this.items = res.data
                    this.loading = false
                })
                .catch(err => {
                    this.loading = false
                })
        }, 800),
        getWarehouseList(query, callback) {
            axios.get('/api/inventory/sales?dropdown=true').then(response => {
                this.sales = response.data;
                callback(query);
            }).catch(error => {

            })
        },
        returnsFromCreate(action, identifier) {
            console.log(action, identifier)
            if (action === 'view') {
                this.createOrUpdateDialog = false
                this.show_id = identifier
            }
            if(action==='edit'){
                this.show_id = null
                this.edit_id = identifier
                this.createOrUpdateDialog = true
            }
        },
        editBill(id){
            this.payment_crud_payment_id = id
            this.payment_crud_method = 'edit'
            this.payment_crud_dialog = true
            this.payment_list_dialog = false
        }
    }
}