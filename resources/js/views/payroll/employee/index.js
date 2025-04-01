import _ from "lodash";
import EmployeeCreate from './create.vue'

export default {
    components: {
        EmployeeCreate
    },
    data() {
        return {
            items               : {},
            loading             : false,
            options             : {
                sortBy      : ['name'],
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            createOrUpdateDialog: false,
            edit_id             : null,
            headers             : [
                {text: 'name', value: 'name', sortable: true},
                {text: 'id', value: 'employee_id', sortable: true},
                {text: 'department', value: 'department_name', sortable: true},
                {text: 'designation', value: 'designation', sortable: true},
                {text: 'mobile', value: 'mobile', sortable: false},
                {text: 'salary', value: 'salary', sortable: true},
                {text: 'contract type', value: 'contract_type', sortable: true},
                {text: 'join date', value: 'join_date', sortable: true},
                {text: 'action', value: 'action', sortable: false},
            ],
            closeOnContentClick : false

        }
    },
    watch     : {
        options: {
            deep: true,
            handler() {
                this.getDataWithLoading()
            }
        },
        createOrUpdateDialog(val) {
            if(!val) {
                this.getDataWithLoading()
                this.edit_id = null
            }
        }
    },
    methods   : {
        deleteItem(id) {
            this.loading = true
            this.$deleteWithConfirmation({
                text: 'Are you sure you want delete this employee?',
                url : api_payroll_url + '/employee/' + id
            })
                .then(data => {
                    this.loading = false
                    this.getData()
                })
                .catch(res => {
                    this.loading = false
                })
        },
        getData: _.debounce(function () {
            axios.get(api_payroll_url + '/employee', {
                params: this.options
            })
                .then(res => {
                    this.items   = res.data
                    this.loading = false
                })
        }, 400),

        getExpenseList(query, callback) {
            axios.get(api_payroll_url + '/employee?dropdown=true')
                .then(response => {
                    this.employee = response.data
                    callback(query)
                }).catch(error => {

            })
        },
        getDataWithLoading() {
            this.loading = true
            this.getData()
        }
    }
}