export default {
    data() {
        return {
            menu: false,
            menu1: false,
            password: '',
            password_confirmation: ''
        }
    },
    computed: {
        sidebar_color() {
            return this.$store.state.settings.settings.design.sidebar_color
        },
        topbar_color() {
            return this.$store.state.settings.settings.design.topbar_color
        },
        swatchStyle() {
            const {sidebar_color, menu} = this
            return {
                backgroundColor: sidebar_color,
                cursor: 'pointer',
                height: '30px',
                width: '30px',
                borderRadius: menu ? '50%' : '4px',
                transition: 'border-radius 200ms ease-in-out'
            }
        },
        swatchStyle1() {
            const {topbar_color, menu1} = this
            return {
                backgroundColor: topbar_color,
                cursor: 'pointer',
                height: '30px',
                width: '30px',
                borderRadius: menu1 ? '50%' : '4px',
                transition: 'border-radius 200ms ease-in-out'
            }
        },
    },
    methods: {
        postCompanyDetail(scope) {
            this.$validator.validateAll(scope).then(result => {
                if (result) {
                    this.$store.dispatch('settings/postCompanyDetail')
                        .catch(error => {
                            let err
                            let errs = error.errors
                            for (err in errs) {
                                this.errors.add({
                                    'field': err,
                                    'msg': errs[err][0],
                                    'scope': scope
                                })
                            }
                        })
                }
            })
        },
        postSettings(scope) {
            this.$validator.validateAll(scope).then(result => {
                if (result) {
                    this.$store.dispatch('settings/postSettings')
                        .catch(error => {
                            let err
                            let errs = error.errors
                            for (err in errs) {
                                this.errors.add({
                                    'field': err,
                                    'msg': errs[err][0],
                                    'scope': scope
                                })
                            }
                        })
                }
            })
        }
    }
}