import _ from "lodash";

export default {
    data() {
        return {
            failed         : {},
            dropzoneOptions: {
                url           : '/api/payroll/employee/avatar',
                thumbnailWidth: 150,
                maxFilesize   : 0.5,
                acceptedFiles : "image/*",
                maxFiles      : 1,
                headers       : {
                    "My-Awesome-Header": "header value",
                    'X-CSRF-TOKEN'     : document.head.querySelector('meta[name="csrf-token"]').content
                }
            },
            loading        : false,
            employee       : {},
            items          : {},
            options        : {
                employeesPerPage: this.$store.state.itemsPerPage,
            },
            headers        : [
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
            ],
        }
    },
    watch  : {
        options: {
            deep: true,
            handler() {
                this.loading = true
                this.salaryDetail()
            }
        }
    },
    created() {
        this.getData()
    },
    methods: {
        uploadError(file, message) {
            file.previewElement
                .querySelectorAll('.dz-error-message span')[0]
                .textContent = message.message
        },
        uploadSuccess(file, message) {
            this.getData()
        },
        salaryDetail: _.debounce(function () {
            this.loading    = true
            this.options.id = this.$route.params.id
            axios.get('/api/payroll/salary', {
                params: this.options
            })
                 .then(res => {
                     this.items   = res.data.salaries
                     this.loading = false

                 })
        }, 200),
        getData() {
            axios
                .get('/api/payroll/employee/' + this.$route.params.id)
                .then(res => {
                    this.employee = res.data.employee
                    setTimeout(() => {
                        this.$refs.myVueDropzone.dropzone.options.url = '/api/payroll/employee/avatar/' + this.employee.id
                    }, 200)
                })
        }
    }
}