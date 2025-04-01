import _ from 'lodash'
import ProductCreate from './create.vue'

export default {
    components: {
        ProductCreate
    },
    data() {
        return {
            name: '',
            code: '',
            items: [],
            search: '',
            edit_id: null,
            dense: false,
            loading: true,
            dialog: false,
            brands: null,
            categories: null,
            showFilter: false,
            collapseOnScroll: true,
            warehouseCreateDialog: false,
            options: {
                itemsPerPage: 10,
            },
            headers: [
                {text: 'Name', align: 'left', sortable: true, value: 'name',},
                {text: 'Code', value: 'code'},
                {text: 'Buying Price', value: 'buying_price'},
                {text: 'Selling Price', value: 'price'},
                {text: 'brand', value: 'brand', sortable: false},
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
        warehouseCreateDialog(val) {
            if (!val) {
                this.getItemsList()
                this.edit_id = null
            }
        },
    },
    created() {
        axios.get('/api/inventory/brands', {
            params: {
                dropdown: true,
                type: 'PRODUCT'
            }
        })
            .then(res => {
                this.brands = res.data
            })

        axios.get('/api/inventory/categories', {
            params: {
                dropdown: true
            }
        })
            .then(res => {
                this.categories = res.data
            })
    },
    methods: {
        resetFilter() {
            this.buying_price = []
            this.selling_price = []
        },
        getItemsList: _.debounce(function () {
            this.loading = true
            axios.get('/api/inventory/products', {
                params: this.options
            }).then(response => {
                this.items = response.data.products
                this.items.total = response.data.products.total
                this.loading = false
            }).catch(error => {

            })
        }, 400),
        postItem(scope) {
            this.$validator.validateAll(scope).then(result => {
                if (result) {
                    !this.forms.id
                        ? this.saveItem(scope)
                        : this.updateItem(scope)
                }
            })
        },
        editItem(id) {
            this.edit_id = id
            this.warehouseCreateDialog = true
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
                    axios.delete('/api/inventory/products/' + id).then(res => {
                        if (res.data.type !== 'error') {
                            swal.fire(
                                'Deleted!',
                                'Product has been deleted successfully',
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
