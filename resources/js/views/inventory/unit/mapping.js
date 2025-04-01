import _ from 'lodash'
import createMapping from './createMapping.vue'
export default {
    components:{
        createMapping
    },
    data() {
        return {
            dense: false,
            collapseOnScroll: true,
            name: '',
            code: '',
            items: [],
            dialog: false,
            edit_id:null,
            loading: true,
            dropdowns: [],
            options: {
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            headers: [
                {
                    text: 'from unit',
                    align: 'left',
                    sortable: false,
                    value: 'from_unit',
                },
                {text: 'to unit', value: 'to_unit', sortable: false},
                {text: 'conversion factor', value: 'conversion_factor'},
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
            if(!val) {
                this.getItemsList()
                this.edit_id = null
            }
        }
    },
    methods: {
        getItemsList: _.debounce(function () {
            this.loading = true
            axios.get('/api/inventory/unitconversions', {
                params: this.options
            }).then(response => {
                this.items = response.data
                this.loading = false
            }).catch(error => {

            })
        }, 200)

    }
}