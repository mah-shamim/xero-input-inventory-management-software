export default {
    props: {
        value: {
            type: Boolean,
            required: true,
            default: false
        },
        modelId: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            forms: {
                employee_id: null,
                salary_date: moment(new Date()).format('YYYY-MM-DD'),
                amount:0.0,
                festival_bonus:0.0,
                other_bonus:0.0,
                deduction:0.0,
            },
            salary: [],
            employees: [],
            payment_types: ['cash'],
            computedSalaryDate: null,
            loading: false,
            modal: false,
            menu1: false,
            menu2: false,
            date: '',
            menu: false,
        }
    },
    computed: {
        method() {
            return this.modelId ? 'patch' : 'post';
        },
        isLoaded() {
            return this.method === 'patch' ? !_.isEmpty(this.salary) : true;
        },
        totalAmount() {
            let amount = 0
            amount = this.$root.ownParse(this.forms.amount) +
                this.$root.ownParse(this.forms.festival_bonus) +
                this.$root.ownParse(this.forms.other_bonus) -
                this.$root.ownParse(this.forms.deduction)
            return amount ? amount.toFixed(2) : 0
        },
        showModal: {
            get() {
                return this.value;
            },
            set(value) {
                if (!value) this.$emit('input', false)
            }
        }
    },

    created() {
        // this.forms.salary_date = moment(new Date()).format('YYYY-MM-DD')
        this.forms.salary_month = moment(new Date()).format('YYYY-MM')
        this.forms.payment_method = 1
        let url = '/api/payroll/salary/create';
        if (this.method === 'patch') {
            url = '/api/payroll/salary/' + this.modelId + '/edit'
        }
        axios.get(url)
            .then(res => {
                if(this.method === 'patch'){
                    this.salary = res.data.salary
                    this.forms.id = this.salary.id
                    this.forms.salary_date    = moment(res.data.salary.salary_date, this.$store.getters['settings/getDefaultDateFormat']).format("YYYY-MM-DD")
                    this.computedSalaryDate   = moment(this.forms.salary_date, "YYYY-MM-DD").format(this.$store.getters['settings/getDefaultDateFormat'])
                    this.forms.employee_id = parseInt(this.salary.employee_id)
                    this.forms.payment_method = this.salary.payment_method
                    this.forms.salary_month = this.salary.salary_month ? moment(new Date(this.salary.salary_month)).format('YYYY-MM') : ''
                    this.forms.amount = parseFloat(this.salary.amount).toFixed(2)
                    this.forms.festival_bonus = parseFloat(this.salary.festival_bonus).toFixed(2)
                    this.forms.other_bonus = parseFloat(this.salary.other_bonus).toFixed(2)
                    this.forms.deduction = parseFloat(this.salary.deduction).toFixed(2)
                    this.forms.note = this.salary.note
                    this.forms.amount         = res.data.salary.current_salary
                }
                this.employees = res.data.employees
            })

    },

    methods: {
        postSalary(scope = 'forms') {
            this.loading = true
            this.forms.salary_date = this.forms.salary_date ?
                moment(this.forms.salary_date).format("YYYY-MM-DD HH:mm:ss") : ''
            this.forms.salary_month = this.forms.salary_month ?
                moment(this.forms.salary_month).format("YYYY-MM-DD") : ''
            let url = '/api/payroll/salary'
            if (this.method === 'patch') {
                url = '/api/payroll/salary/' + this.modelId
            }
            this.$validator.validateAll(scope)
                .then(result => {
                    if (result) {
                        axios[this.method](url, this.forms)
                            .then(res => {
                                swal({
                                    type: res.data.type,
                                    timer: 2000,
                                    text: res.data.message,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                })
                                this.loading = false
                                this.showModal = false
                            })
                            .catch(error => {
                                let err
                                let errs = error.response.data.errors
                                for (err in errs) {
                                    this.errors.add({
                                        'field': err,
                                        'msg': errs[err][0],
                                        scope: scope
                                    })
                                }
                                this.loading = false
                            })
                    } else {
                        this.loading = false
                    }
                })
        },
        getSalary() {
            let currentEmployee = this.employees.filter(data => this.forms.employee_id === data.id)
            this.forms.amount = parseInt(currentEmployee.length ? currentEmployee[0].salary : 0)
            this.forms.current_salary = parseInt(currentEmployee.length ? currentEmployee[0].salary : 0)
        },

    }
}
