import _ from 'lodash'

export default {
    data: () => ({
        active: [],
        types: ["PRODUCT", "EXPENSE", "CUSTOMER", "PRICE"],
        items: [],
        parents:[],
        forms: {},
        open: [1, 2],
        search: null,
        caseSensitive: false,
    }),
    computed: {
        filter() {
            return this.caseSensitive
                ? (item, search, textKey) => item[textKey].indexOf(search) > -1
                : undefined
        }
    },
    watch: {
        active(val) {
            if(val.length>0){
                let obj = this.parents.filter(data=>data.id === val[0])[0]
                this.forms.id=obj.id
                this.forms.name=obj.name
                this.forms.description=obj.description
                this.forms.type=obj.type
                this.forms.parent_id= parseInt(obj.parent_id)
            }
        }
    },
    created() {
        this.forms.type = "PRODUCT"
        this.getItemsList()
    },
    methods:{
        getItemsList:_.debounce(function(){
            axios.get('/api/inventory/categories')
                .then(res => {
                    this.items = res.data
                })
                .catch()
            axios.get('/api/inventory/categories/create')
                .then(res => {
                    this.parents = res.data.categories
                })
                .catch(err => {

                })
        }, 400),
        postItem(scope){
            this.$validator.validateAll(scope).then(result => {
                if (result) {
                    if(!this.forms.parent_id){
                        this.forms.parent_id = 0
                    }
                    if(this.forms.hasOwnProperty('id') && this.forms.id !== undefined && this.forms.id !== null){
                        this.updateItem(scope)
                    }else{
                        this.saveItem(scope)
                    }

                }
            })
        },
        saveItem(scope){
            axios.post('/api/inventory/categories', this.forms)
                .then(res => {
                    swal({
                        type: res.data.type,
                        timer: 2000,
                        text: res.data.message,
                        showCancelButton: false,
                        showConfirmButton: false,
                    }).catch(swal.noop)
                    if (res.data.type === 'success') {
                        this.getItemsList()
                        this.forms = {}
                    }
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
                })
        },
        updateItem(scope){
            axios.patch('/api/inventory/categories/' + this.forms.id, this.forms)
                .then(res => {
                    swal({
                        type: res.data.type,
                        timer: 2000,
                        text: res.data.message,
                        showCancelButton: false,
                        showConfirmButton: false,
                    }).catch(swal.noop)
                    this.dialog = false
                    if (res.data.type === 'success') {
                        this.getItemsList()
                        this.forms = {}
                    }
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
                })
        },
        deleteItem(){
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    axios.delete('/api/inventory/categories/' + this.forms.id).then(res => {
                        if (res.data.type !== 'error') {
                            swal.fire(
                                'Deleted!',
                                'Category has been deleted successfully.',
                                'success'
                            ).catch(swal.noop)
                            this.getItemsList()
                            this.forms={}
                        } else {
                            swal.fire(
                                'Sorry',
                                res.data.message,
                                'error'
                            ).catch(swal.noop)
                        }

                    })
                }
            })
        }
    }
}
