export default {
    data() {
        return {
            forms: {
                id: null
            },
            dense:false,
            units: [],
            search: '',
            searchProduct: null,
            productUnitData: [],
            products: [],
            unitList: [],
            items: [],
            loading: false,
            isLoading: false,
            dialog: false,
            options: {
                itemsPerPage: 10,
            },
            headers: [
                {
                    text: 'Name',
                    align: 'left',
                    sortable: true,
                    value: 'name',
                },
                {text: 'Unit', value: 'unit_name'},
                // {text: 'Action', value: 'action'},

            ],
        }
    },

    beforeCreate() {
        // this.$root.$data.forms.fieldReset();
    },
    created() {
        // this.getListData(1)
        let url = '/api/inventory/productunit/create'
        axios.get(url).then(res => {
            this.units = res.data.units
            this.products=res.data.products
        })
    },
    watch: {
        options: {
            handler() {
                this.getItemsList()
            },
            deep: true,
        },
        searchProduct: _.debounce(function (val) {
            this.isLoading = true
            if (val) {
                this.getProducts(val)
            }
            this.isLoading = false
        }, 400)
    },
    methods: {
        show() {
        },
        getItemsList: _.debounce(function () {
            axios.get('/api/inventory/product-unit/getProductUnits',{
                params:this.options
            }).then(res => {
                this.items = res.data
            })
        }, 400),
        postItem(scope) {
            this.$validator.validateAll(scope).then(result => {
                if (result) {
                    this.forms.unitidjoin = this.forms.unitList.join()
                    axios.post("/api/inventory/productunit", this.forms)
                        .then(res => {
                            if (res.data.type === 'success') {
                                this.forms = {}
                                this.dialog=false
                                this.getItemsList()
                                swal({
                                    type: res.data.type,
                                    timer: 2000,
                                    text: res.data.message,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                }).catch(swal.noop)
                            }
                        })
                        .catch(err => {
                        });
                }
            })
        },

        getProducts(query) {
            if (query !== '') {
                axios.get('/api/inventory/products/getProducts?val=' + query)
                    .then(res => {
                        this.products = res.data.products
                        this.isLoading = false
                    })
                    .catch(error => {
                        alert(error.message)
                    })
            } else {
                this.isLoading = true;
                this.products = []
                return null
            }
        },
        deleteItem(id){
            this.loading = true
            this.$deleteWithConfirmation({
                text: 'Are you sure you want delete this Product Unit conversion?',
                url: "/api/inventory/productunit/" + id
            })
                .then(data => {
                    this.getData()
                    this.loading = false
                })
                .catch(error => {
                    let err = error.response.data.errors
                    swal({
                        type:'error',
                        text:err.message,
                        timer: 4000
                    }).then(r=>{
                        this.loading=false
                    })
                })
        }
    }
}