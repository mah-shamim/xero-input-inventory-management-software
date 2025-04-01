export default {
    data() {
        return {
            forms: this.$root.$data.forms,
            units: [],
            method: '',
            button: {
                text: 'Submit',
                cancel: 'Reset'
            },
            title: {
                text: 'ADD'
            }
        }
    },

    beforeCreate() {
        this.$root.$data.forms.fieldReset();
    },

    watch: {
        '$route.fullPath'(val) {
            //reset data
            this.forms.reset();
            this.units = []
            this.method = 'Create'
            this.button.text = "Submit"
            this.button.cancel = "Reset"
            this.title.text = "ADD"
        }

    },


    computed: {
        isLoaded() {
            if (this.method === 'Edit') {

                return !_.isEmpty(this.units)

            } else {
                return true
            }
        }
    },

    created() {
        this.method = this.$root.getMethodAction();
        if (this.method === 'Edit') {
            this.button.text = "Update"
            this.button.cancel = "Cancel"
            this.title.text = "UPDATE"
            let url = '/api/inventory/units/' + this.$route.params.id + '/edit'
            this.$root.$data.erp.request.get(url, this, (data) => {
                this.units = data.units
                this.forms.id = this.units.id
                this.forms.key = this.units.key
                this.forms.description = this.units.description
                this.forms.is_primary = this.units.is_primary=='0'?0:1

            })
        }
    },

    methods: {
        postUnit() {
            let requestMethod = 'post'
            let url = '/api/inventory/units'
            if (this.method === 'Edit') {
                requestMethod = 'patch'
                url = '/api/inventory/units/' + this.forms.id
            }
            this.forms.submit(requestMethod, url, true, this.$root)
                .then(data => {
                    swal({
                        type: data.type,
                        timer: 2000,
                        text: data.message,
                        showCancelButton: false,
                        showConfirmButton: false,
                    }).catch(swal.noop)
                }).catch(error => {
            });
        },
        onCancel() {
            this.forms.reset();
            if (this.method == "Edit") {
                this.$router.push({name: "unitList"})
            }
        }

    }
}