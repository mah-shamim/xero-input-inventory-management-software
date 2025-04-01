// import PurchaseExport from './purchase-export.vue'
import CreatePurchase from './create.vue'
import ShowPurchase from './show.vue'
import ReturnPurchase from './returns.vue'
import {mapGetters} from "vuex";

export default {
    components: {
        // PurchaseExport,
        CreatePurchase,
        ShowPurchase,
        ReturnPurchase
    },
    data() {
        return {
            columnMenu: false,
            export_dialog: false,
            payment_crud_method: 'create',
            payment_crud_model_id: null,
            payment_crud_dialog: false,
            payment_crud_payment_id: null,
            payment_list_dialog: false,
            menu: false,
            items: {},
            show_id: null,
            isShowFilter:true,
            payments: [],
            loading: false,
            dense: false,
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
            createOrUpdateDialog: false,
            edit_id: null,
            return_id:null,
            headers: [
                {text: 'bill date', value: 'purchase_date', sortable: true, align: 'center'},
                {text: 'bill no', value: 'bill_no', sortable: true, align: 'center'},
                {text: 'product count', value: 'products_count', sortable: true, align: 'center'},
                {text: 'payment count', value: 'payments_count', sortable: true, align: 'center'},
                {text: 'company-id', value: 'supplier_company_id', sortable: false, align: 'center'},
                {text: 'amount', value: 'total', sortable: true, align: 'center '},
                {text: 'total paid', value: 'total_paid', sortable: true, align: 'center    '},
                {text: 'due', value: 'due', sortable: true, align: 'center  '},
                {text: 'weight', value: 'total_weight', sortable: true, align: 'center  '},
                {text: 'created at', value: 'created_at', sortable: true, align: 'center    '},
                {text: 'action', value: 'action', sortable: false, align: 'center   '}
            ],
            closeOnContentClick: true
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
        payment_crud_dialog(val) {
            if (!val) this.resetPaymentComponent()
        },
        createOrUpdateDialog(val) {
            if (!val) {
                this.getData()
                this.edit_id = null
            }
        },
        return_id(val){
            if(!val) this.getData()
        },
        showPurchaseDialog(val) {
            if (!val) this.show_id = null
        }
    },
    computed:{
        ...mapGetters({defaultPurchasePaymentMethod:'settings/getPurchaseDefaultPaymentMethod'})
    },
    methods: {
        uploadFile() {
            let importHeaders = [
                'bill no', //0
                'supplier id', //1
                'total price', //2
                'shipping cost', //3
                'overall discount', //4
                'purchase date', //5
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
            ];
            let formData = new FormData()
            let file = this.$refs.inputUpload.files[0]
            formData.append('file', file)
            axios.post('/api/inventory/purchases-import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(res => {
                    Swal.fire({
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
                            let html = '<p>' + listNumber + '. Incorrect entry at row: ' + row + ', column: ' + importHeaders[column] + '.</p>'
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
                this.export_dialog = false
            }
        },

        getListOfBills(id) {
            this.payments = []
            axios
                .get('/api/payments/bill-paid', {
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
        createBill() {
            this.resetPaymentComponent()
            this.payment_crud_dialog = true
        },
        createBillById(id) {
            this.method = 'create'
            this.payment_crud_model_id = id
            this.payment_crud_dialog = true
        },
        editBill(id) {
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
                text: 'Are you sure you want delete this purchase?',
                url: '/api/inventory/purchases/' + id
            })
                .then(data => {
                    this.getData()
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })

        },

        returnsFromCreate(action, identifier){
            if(action==='view'){
                this.createOrUpdateDialog = false
                this.show_id = identifier
            }
        },
        getData: _.debounce(function () {
            this.loading = true
            axios.get('/api/inventory/purchases', {
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
            axios.get('/api/inventory/purchases?dropdown=true')
                .then(response => {
                    this.purchase = response.data;
                    callback(query);
                })
                .catch(error => {

                })
        },
    }
}