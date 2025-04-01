import barcodePrint from '../../utility/barcode.vue'
export default {
    components:{barcodePrint},
    data() {
        return {
            modal1:false,
            message: '',
            category_ids:[],
            forms: this.$root.$data.forms,
            brands: [],
            units: [],
            products: [],
            method: '',
            button: {
                text: 'Submit',
                cancel: 'Reset'
            },
            title: {
                text: 'ADD'
            }
        }
    },

    beforeCreate() {
        this.$root.$data.forms.fieldReset();
    },

    watch: {
        '$route.fullPath'(val) {
            //reset data
            this.forms.reset();
            this.products = []
            this.method = 'Create'
            this.button.text = "Submit"
            this.button.cancel = "Reset"
            this.title.text = "ADD"
        }

    },

    created() {
        this.method = this.$root.getMethodAction();
        let url = '/api/inventory/products/create?dropdown=true&withJsonFormat=true';
        if (this.method === 'Edit') {
            url = '/api/inventory/products/' + this.$route.params.id + '/edit'
            this.button.text = "Update"
            this.button.cancel = "Cancel"
            this.title.text = "UPDATE"
        }
        this.$root.$data.erp.request.get(url, this, (data) => {
            this.categories = data.categories
            this.brands = data.brands
            this.units = data.units
            if (this.method === 'Edit') {
                this.products = data.products
                this.forms.id = this.products.id
                this.forms.brand = this.products.brands.id
                this.forms.name = this.products.name
                for (var i = 0; i < this.products.categories.length; i++) {
                    let category = this.products.categories[i]
                    this.category_ids.push(category.id)
                }
                this.forms.categories = this.category_ids
                this.forms.code = this.products.code
                this.forms.slug = this.products.slug
                this.forms.buying_price = this.products.buying_price
                this.forms.price = this.products.price
                this.forms.base_unit_id = parseInt(this.products.base_unit_id)
                this.forms.measurement = this.products.measurement
            }
        })
    },
    methods: {
        generateBarcode(){
            this.$store.commit('barcodeValue', this.forms.code)
            this.$router.push({name:'utility.barcode'})
        },
        postProduct() {
            let requestMethod = 'post'
            let url = '/api/inventory/products'
            if (this.method === 'Edit') {
                requestMethod = 'patch'
                url = '/api/inventory/products/' + this.forms.id
            }
            this.forms.categories = this.category_ids
            this.forms.submit(requestMethod, url, true, this.$root)
                .then(data => {
                    swal({
                        type: data.type,
                        timer: 2000,
                        text: data.message,
                        showCancelButton: false,
                        showConfirmButton: false
                    }).catch(swal.noop)
                }).catch(error => {
            });
        },

        categoryIds: function (cat_ids) {
            this.category_ids = cat_ids
        },
        onCancel() {
            this.forms.reset();
            if (this.method == "Edit") {
                this.$router.push({name: "productsIndex"})
            }
        }
    }
}