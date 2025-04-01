import _ from "lodash";
import SalaryChart from './components/salaryChart.vue'
// import SalaryExport from './salary-export.vue'
import SalaryCreate from './create.vue'

export default {
    components: {
        SalaryChart,
        // SalaryExport,
        SalaryCreate
    },
    data() {
        return {
            exportDialog: false,
            items: {},
            loading: false,
            options: {
                sortBy: ['salary_date'],
                itemsPerPage: this.$store.state.itemsPerPage,
            },
            total_amount: {
                total_current_salary: 0,
                total_paid: 0,
                total_due: 0
            },
            createOrUpdateDialog: false,
            edit_id: null,
            headers: [
                {text: 'name', value: 'employee_name', sortable: false},
                {text: 'salary month', value: 'salary_month', sortable: true},
                {text: 'salary paid', value: 'salary_paid', sortable: true},
                {text: 'festival bonus', value: 'festival_bonus', sortable: true},
                {text: 'other bonus', value: 'other_bonus', sortable: false},
                {text: 'deduction', value: 'deduction', sortable: false},
                {text: 'total', value: 'total', sortable: true},
                {text: 'salary', value: 'current_salary', sortable: true},
                {text: 'due', value: 'due', sortable: true},
                {text: 'salary date', value: 'salary_date', sortable: true},
                {text: 'created at', value: 'created_at', sortable: true},
                {text: 'action', value: 'action', sortable: false},
            ],
            closeOnContentClick: false,
            modal: false,
            menu1: false,
            menu2: false,
            departments: []
        }
    },
    watch: {
        options: {
            deep: true,
            handler() {
                this.getDataWithLoading()
            }
        },
        createOrUpdateDialog(val) {
            if (!val) {
                this.getDataWithLoading()
                this.edit_id = null
            }
        }
    },
    computed: {
        pageTotal_current_salary() {
        }
    },
    created() {
        axios.get(api_payroll_url + '/salary-total')
            .then(res => {
                this.total_amount = res.data
            })
        if (!_.isEmpty(this.$route.params) && this.$route.params.id) {
            this.options.id = this.$route.params.id
        }

        axios.get(api_payroll_url + '/department', {
            params: {
                'no-paginate': true
            }
        })
            .then(res => {
                this.departments = res.data.departments
            })

    },

    methods: {
        uploadFile() {
            let formData = new FormData()
            let file = this.$refs.inputUpload.files[0]
            formData.append('file', file)
            axios.post('/api/inventory/salary-import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(res => {
                    swal.fire({
                        type: 'success',
                        text: 'file has been uploaded successfully, please update payments as well'
                    }).then((result) => {
                        this.getData()
                    })
                })
                .catch(error => {
                    swal.fire({
                        type: 'error',
                        html: 'status ' + error.response.status + '.<br>' + error.response.statusText,
                    })
                })
        },
        closeDialogExport(val) {
            if (val === 200) {
                this.exportDialog = false
            }
        },
        deleteItem(id) {
            this.loading = true
            this.$deleteWithConfirmation({
                text: 'Are you sure you want delete this salary?',
                url: api_payroll_url + '/salary/' + id
            })
                .then(data => {
                    this.getData()
                    this.loading = false
                })
                .catch(error => {
                    this.loading = false
                })
        },
        getData: _.debounce(function () {
            axios.get(api_payroll_url + '/salary', {
                params: this.options
            })
                .then(res => {
                    this.items = res.data.salaries
                    this.loading = false

                })
        }, 400),

        moment() {
            return moment();
        },
        getDataWithLoading() {
            this.loading = true
            this.getData()
        }
    }
}