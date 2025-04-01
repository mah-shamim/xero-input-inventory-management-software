<template>

    <div class="content-area">
        <div class="heading">
            <div class="reportHandle">
                <div class="int-title">Sales Return</div>
            </div>
        </div>
        <div class="row pdt-10">
            <table class="table stripe">
                <thead>
                <tr>
                    <td>No.</td>
                    <td>Code</td>
                    <td>Name</td>
                    <td>Warehouse</td>
                    <td>Quantity</td>
                    <td>Unit Cost</td>
                    <td>Discount</td>
                    <td class="bg-warning-lt">Total</td>
                    <td>status</td>
                    <td>Amount</td>
                    <td>
                        unit
                    </td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(product, index) in rproduct">
                    <td>
                        {{ index + 1 }}
                    </td>
                    <td>{{product.code}}</td>
                    <td>{{product.name}}</td>
                    <td>
                        <Select v-model="product.warehouse" filterable
                                placeholder="Select Warehouse">
                            <Option v-for="item in warehouses" :value="item.id" :key="item.id">
                                <span> {{ item.name }} </span>
                            </Option>
                        </Select>
                    </td>
                    <td>{{product.quantityStr}}</td>
                    <td>{{product.pivot.price}}</td>
                    <td>{{product.pivot.discount}}</td>
                    <td class="bg-warning-lt strong">{{product.pivot.subtotal}}</td>
                    <td>{{$root.productStatus(sale.status)}}</td>
                    <td>
                        <!--amount-->
                        <input type="number"
                               step="any"
                               v-model="product.ramount"
                               class="input-item"
                        >
                    </td>
                    <td>
                        <table class="table striped">
                            <tr>
                                <!--unit-->
                                <td v-for="unit in product.units">
                                    <span>{{unit.key}}</span>
                                    &nbsp;
                                    <input type="number" v-model="unit.runit" step="any" class="unit-input">
                                </td>

                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <button class="info s" @click="submit">Submit</button>
        </div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                sale: {},
                value: null,
                amount: 0,
                rproduct: [],
                warehouses: []
            }
        },
        created() {
            let url = '/api' + window.location.pathname
            console.log(url);
            this.$root.$data.erp.request.get(url, this, (data) => {
                this.sale = data.sale
                this.rproduct = this.sale.products
                this.warehouses = data.warehouses
            })
        },
        methods: {
            submit() {
                let x = 0;
                let firstCount = 0;
                let obj = {};
                for (let i = 0, len = this.rproduct.length; i < len; i++) {
                    let product = this.rproduct[i]
                    for (let j = 0; j < product.units.length; j++) {
                        let unit = product.units[j];
                        // console.log(unit);
                        if (unit.runit === undefined || unit.runit === '') continue
                        if (product.ramount === undefined || product.ramount === '') continue
                        if (product.warehouse === undefined || product.warehouse === '') continue
                        if (firstCount > 0) product.ramount = 0
                        obj[x] = {
                            returnable_id: parseInt(this.$root.$route.params.id),
                            returnable_type: 'App\\Inventory\\Sale',
                            product_id: product.id,
                            unit_id: unit.id,
                            quantity: unit.runit,
                            warehouse_id: product.warehouse,
                            amount: parseFloat(product.ramount)
                        }
                        firstCount++;
                        x++;
                    }
                    firstCount = 0;
                }
                // console.log(obj);
                axios.post('/api/inventory/sale/' + this.$root.$route.params.id + '/return', {
                    return: obj
                }).then(response => {
                    swal({
                        type: response.data.type,
                        timer: 2000,
                        text: response.data.message,
                        showCancelButton: false,
                        showConfirmButton: false,
                    }).catch(swal.noop)
                }).catch(error => {
                });
            }
        }
    }
</script>

<style scoped>

    .input-item, .ivu-input, .ivu-select-input {
        height: 24px !important;
    }

    .unit-input {
        line-height: 1.5;
        border: 1px solid #dedede;
        border-radius: 3px;
        padding-left: 0.3125rem;
        width: 30%;
    }

</style>