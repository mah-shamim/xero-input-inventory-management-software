import _ from 'lodash'

export default {
    data() {
        return {
            collapseOnScroll: true,
            name: '',
            code: '',
            items: [],
            dialog: false,
            dense: false,
            search: '',
            loading: true,
            options: {
                itemsPerPage: 10,
            },
            forms: {
                id: null,
                is_primary:true
            },
            headers: [
                {
                    text: 'Key',
                    align: 'left',
                    sortable: true,
                    value: 'key',
                },
                {text: 'Description', value: 'description'},
                {text: 'created at', value: 'created_at'},
                {text: 'Actions', value: 'action', sortable: false},
            ],
        }
    },

    watch: {
        options: {
            handler() {
                this.getItemsList()
            },
            deep: true,
        },
    },
    methods: {
        getItemsList: _.debounce(function () {
            this.loading = true
            axios.get('/api/inventory/units?' + this.$options.filters.vuetifyOptionString(this.options) + '&search=' + this.search).then(response => {
                this.items = response.data
                this.loading = false
            }).catch(error => {

            })
        }, 400),
        postItem(scope) {
            this.$validator.validateAll(scope).then(result => {
                if (result) {
                    !this.forms.id ? this.saveItem(scope) : this.updateItem(scope)
                }
            })
        },
        saveItem(scope) {
            axios.post('/api/inventory/units', this.forms)
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
                        this.options.itemsPerPage= 5
                        this.getItemsList()
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
        editItem(val) {
            axios.get('/api/inventory/units/' + val + '/edit')
                .then(res => {
                    this.forms = res.data.units
                    this.dialog = true
                })
        },
        updateItem(scope) {
            axios.patch('/api/inventory/units/' + this.forms.id, this.forms)
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
                        this.options.itemsPerPage= 5
                        this.getItemsList()
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
        deleteItem(id) {
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
                    axios.delete('/api/inventory/units/' + id).then(res => {
                        if (res.data.type !== 'error') {
                            swal.fire(
                                'Deleted!',
                                'Unit has been deleted successfully',
                                'success'
                            ).catch(swal.noop)
                            this.options.itemsPerPage= 5
                            this.getItemsList()
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