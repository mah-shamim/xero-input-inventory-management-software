import _ from 'lodash'

export default {
    data() {
        return {
            name: '',
            code: '',
            items: [],
            dense:false,
            dialog: false,
            loading: true,
            options             : {
                search:null,
                sortBy      : ['updated_at'],
                sortDesc    : [true],
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            collapseOnScroll: true,
            id:null,
            headers: [
                {text: 'Product Name', align: 'left', sortable: false, value: 'product_name'},
                {text: 'Customer Name', align: 'left', sortable: false, value: 'customer_name'},
                {text: 'quantity', value: 'quantity'},
                {text: 'Status', value: 'status'},
                {text: 'Note', value: 'note', sortable:false},
                {text: 'warranty date', value: 'warranty_date'},
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
            if(!val){
                this.getItemsList()
            }
        },
    },
    methods: {
        editItem(id){
            if(id){
                this.id=parseInt(id)
                this.dialog=true
            }
        },
        getItemsList: _.debounce(function () {
            this.loading = true
            axios.get('/api/inventory/warranty', {
                params:this.options
            }).then(response => {
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

        deleteItem(id) {
            swal.fire({
                type: 'warning',
                title: 'Are you sure?',
                showCancelButton: true,
                cancelButtonColor: '#d33',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                text: "You won't be able to revert this!"
            }).then((result) => {
                if (result.value) {
                    axios.delete('/api/inventory/warranty/' + id).then(res => {
                        if (res.data.type !== 'error') {
                            swal.fire(
                                'Deleted!',
                                'Warranty has been deleted successfully',
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
