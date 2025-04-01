import createSupplier from '../create.vue'
export default {
    components:{
        createSupplier
    },
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
                sortBy:['created_at']
            },
            edit_id:null,
            headers: [
                {
                    text: 'Name',
                    align: 'left',
                    sortable: true,
                    value: 'name',
                },
                {text: 'Company', value: 'company'},
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
                this.getItemsList()
            },
            deep: true,
        },
        dialog(val){
            if (!val) {
                this.edit_id = null
                this.getItemsList()
            }
        }
    },
    methods: {
        editItem(id){
            this.edit_id = id
            this.dialog = true
        },
        getItemsList: _.debounce(function () {
            this.loading = true
            axios.get('/api/inventory/suppliers', {params:this.options}).then(response => {
                this.items = response.data
                this.loading = false
            }).catch(error => {

            })
        }, 400),
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
                    axios.delete('/api/inventory/suppliers/' + id).then(res => {
                        if (res.data.type !== 'error') {
                            swal.fire(
                                'Deleted!',
                                'Suppliers has been deleted successfully',
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