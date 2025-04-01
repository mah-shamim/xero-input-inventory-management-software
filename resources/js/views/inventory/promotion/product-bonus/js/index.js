export default {
    data() {
        return {
            name: '',
            code: '',
            productBonuses: [],

        }
    },

    computed: {},

    methods: {

        getBrandList(query, callback) {
            axios.get('/api/inventory/product-bonuses?dropdown=true').then(response => {
                this.productBonuses = response.data;
                callback(query);
            }).catch(error => {

            })
        }

    }
}