export default {
    props:{
        value:{
            type:Number,
            default:null,
            required:true,
        }
    },
    data() {
        return {
            purchase  : {},
            amount    : 0,
            rproduct  : [],
            warehouses: [],
        }
    },
    created() {
        this.getData()
    },
    computed:{
        showModal: {
            get() {
                return !!this.value;
            },
            set(value) {
                if (!value) this.$emit('input', null)
            }
        }
    },
    methods: {
        getData(){
            let url = '/api/inventory/purchase-return/' + this.value
            axios.get(url)
                .then(res=>{
                    let data = res.data
                    this.purchase   = data.purchase
                    this.rproduct   = this.purchase.products
                    this.warehouses = data.warehouses
                })
        },
        submit() {
            let x          = 0;
            let firstCount = 0;
            let obj        = [];
            for (let i = 0, len = this.rproduct.length; i < len; i++) {
                let product = this.rproduct[i]
                for (let j = 0; j < product.units.length; j++) {
                    let unit = product.units[j];
                    // console.log(unit);
                    if(unit.runit === undefined || unit.runit === '') continue
                    if(product.ramount === undefined || product.ramount === '') continue
                    if(product.pivot.warehouse_id === undefined || product.warehouse === '') continue
                    if(firstCount > 0) product.ramount = 0
                    obj[x] = {
                        returnable_id  : parseInt(this.value),
                        returnable_type: 'App\\Models\\Inventory\\Purchase',
                        product_id     : product.id,
                        unit_id        : unit.id,
                        quantity       : unit.runit,
                        warehouse_id   : product.pivot.warehouse_id,
                        amount         : parseFloat(product.ramount)
                    }
                    firstCount++;
                    x++;
                }
                firstCount = 0;
            }
            if(!obj.length) {
                swal.fire({
                              icon: 'error',
                              text: 'Data are not valid'
                          })
                return null
            }
            axios.patch('/api/inventory/purchase-return/' + this.value, {
                return: obj
            })
                 .then(response => {
                     swal.fire({
                                   icon             : response.data.type,
                                   timer            : 3000,
                                   text             : response.data.message,
                                   showCancelButton : false,
                                   showConfirmButton: false,
                               })
                         .then(result => {
                             this.$emit('input', null)
                         })

                 })
                 .catch(error => {
                 });
        }
    }
}