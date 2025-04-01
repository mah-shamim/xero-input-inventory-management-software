import ExpensePaymentExport from './expense-payment-export.vue'

export default {
    components: {
        ExpensePaymentExport
    },
    data() {
        return {
            exportDialog       : false,
            payment_bill_dialog: false,
            payment_bill_id    : null,
            payment_id         : null,
            payment_dialog     : false,
            method             : 'create',
            menu               : false,
            items              : {},
            loading            : false,
            options            : {
                sortBy      : ['date'],
                sortDesc    : [true],
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            headers            : [
                {text: 'bill no', value: 'expense_ref'},
                {text: 'user name', value: 'supplier_name', sortable: false},
                {text: 'amount', value: 'paid'},
                {text: 'date', value: 'date'},
                {text: 'action', value: 'action'},
            ],
        }
    },
    watch     : {
        options: {
            deep: true,
            handler() {
                this.loading = true
                this.getData()
            }
        },
    },
    methods   : {
        uploadFile() {
            let formData = new FormData()
            let file     = this.$refs.inputUpload.files[0]
            formData.append('file', file)
            axios.post('/api/inventory/expense-bill-import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                 .then(res => {
                     Swal.fire({
                                   icon: 'success',
                                   text: 'file has been uploaded successfully'
                               }).then((result)=>{
                         this.getData()
                     })
                 })
                 .catch(error => {
                     Swal.fire({
                                   icon: 'error',
                                   html: 'status '+error.response.status+'.<br>'+error.response.statusText,
                               })
                 })
        },
        closeDialogExport(val) {
            if(val === 200) {
                this.export_dialog = false
            }
        },
        getData() {
            axios
                .get('/api/payments/expense-bill-paid', {params: this.options})
                .then(res => {
                    this.items   = res.data
                    this.loading = false
                })
        },
    }
}