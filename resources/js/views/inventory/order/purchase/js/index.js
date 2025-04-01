export default {
    data() {
        return {
            orderNo: '',
            supplierName: '',
            biller: ''
        }
    },


    computed: {
        queryString() {
            return '&order_no=' + this.orderNo + '&supplier_name=' + this.supplierName + '&biller=' + this.biller
        }
    },

    methods: {
        processOrder(selectedItem) {
            if (selectedItem == null) {
                this.$root.showMessage('Please Select an item before process', 'warning')
                return
            }
            axios.post('/api/inventory/order/process/' + selectedItem.id).then(response => {
                this.$root.showMessage(response.data.message);
                this.$eventHub.$emit('refresh')
            }).catch(error => {
                alert(error.message)
            });
        },
        cancelOrder(selectedItem) {
            if (selectedItem == null) {
                this.$root.showMessage('Please Select an item before cancel', 'warning')
                return
            }
            if (!confirm("Are you sure you want to cancel the order?")) {
                return
            }
            axios.post('/api/inventory/order/cancel/' + selectedItem.id).then(response => {
                this.$root.showMessage (response.data.message)
                this.$eventHub.$emit('refresh')
            }).catch(error => {
                alert (error.message)
            })
        },
        confirmOrder(selectedItem){
            if (selectedItem == null) {
                this.$root.showMessage('Please Select an item before confirm', 'warning')
                return
            }
            if (!confirm("Using Confirm this Order will be converted as Sales.\n Are you sure you want to Confirm the order?")) {
                return
            }
            axios.post('/api/inventory/order/confirm/' + selectedItem.id).then(response => {
                this.$root.showMessage(response.data.message)
                this.$eventHub.$emit('refresh')

            }).catch(error => {
                alert(error.message)
            });
        },
        clickTab(name) {
            console.log(name);
        }
    }
}