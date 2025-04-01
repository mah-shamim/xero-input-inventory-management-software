import _ from 'lodash'
import department from "../department/index.vue";

export default {
    props: {
        value: {
            type: Boolean,
            required: true,
            default: false
        },
        editId: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            employee: [],
            forms: {
                id: null,
                nid: null,
                name: null,
                note: null,
                mobile: null,
                salary: null,
                address: null,
                emergency: null,
                employee_id: null,
                designation: null,
                contract_type: null,
                department_id: null,
                birth: moment(new Date()).format('YYYY-MM-DD'),
                join_date: moment(new Date()).format('YYYY-MM-DD'),
            },
            loading: false,
            menu2: false,
            menu3: false,
            method: 'Create',
            modal: false,
            departments:[]
        }
    },

    // beforeCreate() {
    //     this.$root.$data.forms.fieldReset();
    // },

    watch: {
        '$route.fullPath'(val) {
            //reset data
            this.forms.reset();
            this.expenses = []
            this.method = 'Create'

        }

    },

    computed: {
        department() {
            return department
        },
        isLoaded() {
            return this.method === 'Edit' ? !_.isEmpty(this.employee) : true;
        },
        showModal: {
            get() {
                return this.value;
            },
            set(value) {
                if (!value) this.$emit('input', false)
            }
        },
        url() {
            return this.editId
                ? '/api/payroll/employee/' + this.editId + '/edit'
                : '/api/payroll/employee/create';
        }
    },

    created() {
        this.loading = true
        axios.get(this.url)
            .then(res => {
                if (this.editId) {
                    this.employee = res.data.employee
                    this.forms.id = this.employee.id
                    this.departments = res.data.departments
                    this.forms.join_date = this.employee.join_date ? moment(new Date(this.employee.join_date)).format('YYYY-MM-DD') : ''
                    this.forms.employee_id = this.employee.employee_id
                    this.forms.department_id = this.employee.department_id
                    this.forms.name = this.employee.name
                    this.forms.designation = this.employee.designation
                    this.forms.contract_type = this.employee.contract_type
                    this.forms.salary = parseInt(this.employee.salary)
                    this.forms.mobile = this.employee.mobile
                    this.forms.birth = this.employee.birth ? moment(new Date(this.employee.birth)).format('YYYY-MM-DD') : ''
                    this.forms.nid = this.employee.nid
                    this.forms.emergency = this.employee.emergency
                    this.forms.address = this.employee.address
                    this.forms.note = this.employee.note
                }else{
                    this.departments = res.data.departments
                }
                this.loading = false
            })

    },

    methods: {
        postEmployee(scope = 'forms') {
            this.loading = true
            this.forms.birth = this.forms.birth ?
                moment(this.forms.birth).format("YYYY-MM-DD HH:mm:ss") : ''
            this.forms.join_date = this.forms.join_date ?
                moment(this.forms.join_date).format("YYYY-MM-DD HH:mm:ss") : ''
            let method = this.editId?'patch':'post';
            let url = method==='post'?'/api/payroll/employee':`/api/payroll/employee/${this.editId}`

            this.$validator.validateAll(scope)
                .then(result => {
                    if (result) {
                        axios[method](url, this.forms)
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
    }
}
