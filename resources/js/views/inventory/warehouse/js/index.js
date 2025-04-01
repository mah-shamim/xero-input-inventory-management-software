import _ from 'lodash'

export default {
    data() {
        return {
            collapseOnScroll: true,
            name: '',
            code: '',
            warehouses: [],
            dialog: false,
            dense: false,
            search: '',
            loading: true,
            options: {
                itemsPerPage: 10,
            },
            forms: {
                id: null
            },
            headers: [
                {
                    text: 'Name',
                    align: 'left',
                    sortable: true,
                    value: 'name',
                },
                {text: 'Code', value: 'code'},
                {text: 'Phone', value: 'phone', sortable: false},
                {text: 'Email', value: 'email'},
                {text: 'Address', value: 'address'},
                {text: 'created at', value: 'created_at'},
                {text: 'Actions', value: 'action', sortable: false},
            ],
        }
    },

    watch: {
        options: {
            handler() {
                this.getWarehouseList()
            },
            deep: true,
        },
    },
    methods: {
        getWarehouseList: _.debounce(function () {
            this.loading = true
            axios.get('/api/inventory/warehouses?' + this.$options.filters.vuetifyOptionString(this.options) + '&search=' + this.search).then(response => {
                this.warehouses = response.data
                this.loading = false
            }).catch(error => {

            })
        }, 400),
        postWarehouse(scope) {
            this.$validator.validateAll(scope).then(result => {
                if (result) {
                    console.log('testing')
                    !this.forms.id ? this.saveWarehouse(scope) : this.updateWarehouse(scope)
                }
            })
        },
        saveWarehouse(scope) {
            axios.post('/api/inventory/warehouses', this.forms)
                .then(res => {
                    swal({
                        type: res.data.type,
                        timer: 2000,
                        text: res.data.message,
                        showCancelButton: false,
                        showConfirmButton: false,
                    }).catch(swal.noop)
                    this.dialog = false
                    if (res.data.type === 'success') {
                        this.options.sortBy[0]='created_at'
                        this.options.sortDesc[0]='true'
                        this.getWarehouseList()
                        this.options.itemsPerPage= 5
                        this.forms = {}
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
        editWarehouse(val) {
            axios.get('/api/inventory/warehouses/' + val + '/edit')
                .then(res => {
                    this.forms = res.data.warehouses
                    this.dialog = true
                })
        },
        updateWarehouse(scope) {
            axios.patch('/api/inventory/warehouses/' + this.forms.id, this.forms)
                .then(res => {
                    swal({
                        type: res.data.type,
                        timer: 2000,
                        text: res.data.message,
                        showCancelButton: false,
                        showConfirmButton: false,
                    }).catch(swal.noop)
                    this.dialog = false
                    if (res.data.type === 'success') {
                        this.options.sortBy[0]='updated_at'
                        this.options.sortDesc[0]='true'
                        this.getWarehouseList()
                        this.options.itemsPerPage= 5
                        this.forms = {}
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
        deleteWarehouse(id) {
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    axios.delete('/api/inventory/warehouses/' + id).then(res => {
                        if (res.data.type !== 'error') {
                            swal.fire(
                                'Deleted!',
                                'Warehouse has been deleted.',
                                'success'
                            ).catch(swal.noop)
                            this.getWarehouseList()
                        } else {
                            swal.fire(
                                'Sorry',
                                res.data.message,
                                'error'
                            ).catch(swal.noop)
                        }

                    })
                }
            })
        }

    }
}