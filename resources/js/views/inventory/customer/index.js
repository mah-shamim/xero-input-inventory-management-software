import createCustomer from './create.vue'

export default {
    components: {
        createCustomer
    },
    data() {
        return {
            code: '',
            collapseOnScroll: true,
            dense: false,
            dialog: false,
            forms: {
                id: null,
            },
            edit_id: null,
            headers: [
                {text: 'Name', value: 'name'},
                {text: 'Phone', value: 'phone', sortable: false},
                {text: 'Email', value: 'email'},
                {text: 'Address', value: 'address'},
                {text: 'created at', value: 'created_at'},
                {text: 'Actions', value: 'action', sortable: false},
            ],
            items: [],
            loading: true,
            name: '',
            options: {
                itemsPerPage: 10,
                sortBy: ['created_at']
            },
            search: '',
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
            axios.get('/api/inventory/customers', {params: this.options})
                .then(response => {
                    this.items = response.data
                    this.loading = false
                }).catch(error => {

            })
        }, 400),
        updateCustomerList(val) {
            if (this.edit_id) {
                this.options.sortBy[0] = 'updated_at'
                this.options.sortDesc[0] = 'true'
                this.options.itemsPerPage = 5
            } else {
                this.options.sortBy[0] = 'created_at'
                this.options.sortDesc[0] = 'true'
                this.options.itemsPerPage = 5
            }
            this.$nextTick(()=>{
                this.getItemsList()
                this.edit_id = null
                this.dialog = false
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
                    axios.delete('/api/inventory/customers/' + id).then(res => {
                        if (res.data.type !== 'error') {
                            swal.fire(
                                'Deleted!',
                                'Customer has been deleted successfully',
                                'success'
                            ).catch(swal.noop)
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