export default {
    data() {
        return {
            name: '',
            code: '',
            tierPrices: [],

        }
    },

    computed: {},

    methods: {

        getBrandList(query, callback) {
            axios.get('/api/inventory/product-tires-prices?dropdown=true').then(response => {
                this.tierPrices = response.data;
                callback(query);
            }).catch(error => {

            })
        }

    }
}