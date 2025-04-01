export default {
    data() {
        return {
            id:null,
            name: '',
            code: '',
            items: [],
            dense:false,
            loading: true,
            dialog: false,
            sale_value_total:null,
            add_method:'create',
            options: {
                search:null,
                sortBy      : ['updated_at'],
                sortDesc    : [true],
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            collapseOnScroll: true,
            headers: [
                {text: 'Product', align: 'left', sortable: false, value: 'product_name'},
                {text: 'Unit', value: 'unit_name', sortable:false},
                {text: 'Quantity', value: 'quantity'},
                {text:'Sale Value', value:'sale_value'},
                {text: 'created at', value: 'created_at'},
                {text: 'Actions', value: 'action', sortable: false},
            ],
        }
    },

    watch: {
        options: {
            deep: true,
            handler() {
                this.getItemsList()
            },
        },
        dialog(val){
            if(!val) this.getItemsList()
        }
    },
    methods: {
        getItemsList: _.debounce(function () {
            this.loading = true
            axios.get('/api/inventory/productdamages', {
                params:this.options
            }).then(response => {
                this.items = response.data.productDamages
                this.sale_value_total = response.data.sale_value_total
                this.loading = false
            }).catch(error => {

            })
        }, 400),

        editItem(val) {
            this.id = val
            this.add_method = 'edit'
            this.dialog=true
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
                    axios.delete('/api/inventory/productdamages/' + id).then(res => {
                        if (res.data.type !== 'error') {
                            swal.fire(
                                'Deleted!',
                                'Product Damage has been deleted successfully',
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
