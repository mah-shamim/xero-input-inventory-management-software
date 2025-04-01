export default {
    props: {
        value: {
            type: Boolean,
            required: true,
            default: false
        },
        id: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            forms: {
                id: null,
            }
        }
    },
    computed: {
        showModal: {
            get() {
                return this.value;
            },
            set(value) {
                if (!value) this.$emit('input', value)
            }
        }
    },
    watch:{
        id:{
            immediate:true,
            handler(val){
                if(val) this.editItem(val)
            }
        }
    },
    methods: {
        postItem(scope) {
            this.$validator.validateAll(scope).then(result => {
                if (result) {
                    !this.forms.id ? this.saveItem(scope) : this.updateItem(scope)
                }
            })
        },
        saveItem(scope) {
            axios.post('/api/inventory/customers', this.forms)
                .then(res => {
                    swal({
                        type: res.data.type,
                        timer: 2000,
                        text: res.data.message,
                        showCancelButton: false,
                        showConfirmButton: false,
                    }).catch(swal.noop)
                    if (res.data.type === 'success') {
                        this.$emit('updateCustomerList', res.data.customer)
                    }
                })
                .catch(error => {
                    let err
                    let errs = error.response.data.errors
                    for (err in errs) {
                        this.errors.add({
                            'field': err,
                            'msg': errs[err][0],
                            scope: scope
                        })
                    }
                })
        },
        editItem(val) {
            axios.get('/api/inventory/customers/' + val + '/edit')
                .then(res => {
                    this.forms = res.data.customers
                    this.dialog = true
                })
        },
        updateItem(scope) {
            axios.patch('/api/inventory/customers/' + this.forms.id, this.forms)
                .then(res => {
                    swal({
                        type: res.data.type,
                        timer: 2000,
                        text: res.data.message,
                        showCancelButton: false,
                        showConfirmButton: false,
                    }).catch(swal.noop)
                    if (res.data.type === 'success') {
                        this.$emit('updateCustomerList', res.data.customer)
                    }
                })
                .catch(error => {
                    let err
                    let errs = error.response.data.errors
                    for (err in errs) {
                        this.errors.add({
                            'field': err,
                            'msg': errs[err][0],
                            scope: scope
                        })
                    }
                })
        },
    }
}