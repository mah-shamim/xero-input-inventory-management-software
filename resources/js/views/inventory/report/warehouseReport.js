import ProductLocationDialog from 'g~/product-location-dialog.vue'

export default {
    components: {
        ProductLocationDialog
    },
    data() {
        return {
            showProductLocationDialog: false,
            product_id               : null,
            products                 : [],
            part_number_dialog       : false,
            warehouseList            : [],
            queryString              : {} /*TODO: relation query string should be initialized with table names and deep nesting later*/,
            ref                      : "",
            warehouse_id             : null,
            name                     : "",
            code                     : "",
            items                    : {},
            loading                  : false,
            part_number_data         : {},
            options                  : {
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            headers                  : [
                {text: "Product Name", value: "name"},
                {text: "code", value: "code"},
                {text: "quantity", value: "quantityStr", sortable: false},
                {text: "weight", value: "weight", sortable: false},
                {text: "part number", value: "part_number", sortable: false},
            ],
            part_number_headers      : [
                {text: 'bill no', value: 'purchase'},
                {text: 'ref no', value: 'sales'},
                {text: 'part number', value: 'part_number'},
            ],
            part_number_options      : {
                itemsPerPage: this.$store.state.itemsPerPage,
            }
        };
    },
    created() {
        axios.get("/api/report/warehouses/show").then((response) => {
            this.warehouseList = response.data.warehouseList;
        });
        // this.getResults()
    },
    watch     : {
        options            : {
            deep: true,
            handler(val) {
                if(val.warehouse_id !== undefined && val.warehouse_id) {
                    this.getResults();
                }
            },
        },
        part_number_options: {
            deep: true,
            handler(val) {
                this.getPartNumber()
            }
        },
        showProductLocationDialog(val) {
            if(!val) {
                this.product_id = null
            }
        }
    },
    methods   : {
        showPartNumberDialog(product_id) {
            this.part_number_data                 = {}
            this.part_number_options.warehouse_id = this.options.warehouse_id
            this.part_number_options.product_id   = product_id
            this.getPartNumber()
        },
        getPartNumber() {
            axios.get('/api/report/part_numbers', {
                params: this.part_number_options
            })
                .then(res => {
                    this.part_number_data   = res.data
                    this.part_number_dialog = true
                })
        },
        getResults: _.debounce(function () {
            this.loading = true;
            let url      = "/api/report/warehouses";
            axios.get(url, {params: this.options}).then((res) => {
                this.items   = res.data;
                this.loading = false;
            });
        }, 400),
        getTotal() {
            let total = 0;
            for (let i = 0; i < this.warehouses.length; i++) {
                let warehouse = this.warehouses[i];
                for (let j = 0; j < warehouse.products.length; j++) {
                    let product = warehouse.products[j];
                    total += parseFloat(product.pivot.quantity);
                }
            }
            return total.toFixed(4);
        },
    },
};