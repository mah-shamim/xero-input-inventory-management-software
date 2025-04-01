<template>
    <v-row>
        <v-col cols="12" md="6">
            <v-card>
                <v-card-title></v-card-title>
                <v-card-text>
                    <v-container>
                        <combo-chart
                            :type1="'line'"
                            :data1="this.values[1]"
                            :data2="this.values[2]"
                            :labels="this.values[0]"
                            :label="['purchases', 'sales']"
                        ></combo-chart>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" md="6">
            <v-card>
                <v-card-title></v-card-title>
                <v-card-text>
                    <v-container>
                        <single-chart
                            :type="'bar'"
                            :id="'bt10sbp'"
                            :data="this.products[1]"
                            :labels="this.products[0]"
                            :label="'Best Top 10 Sales By Price'"
                        ></single-chart>
                    </v-container>
                </v-card-text>
            </v-card>

        </v-col>
        <v-col cols="12" md="6">
            <v-card>
                <v-card-title></v-card-title>
                <v-card-text>
                    <v-container>
                        <single-chart
                            :type="'bar'"
                            :id="'expense'"
                            :label="'Expense'"
                            :data="this.expenses.expense_totals"
                            :labels="this.expenses.expense_dates"
                        ></single-chart>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" md="6">
            <v-card>
                <v-card-title></v-card-title>
                <v-card-text>
                    <v-container>
                        <single-chart
                            :type="'bar'"
                            :id="'bt10sbqe'"
                            :data="this.product_quantity[1]"
                            :labels="this.product_quantity[0]"
                            :label="'Best Top 10 Sales By Quantity'"
                        ></single-chart>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>
<script>

    export default {
        data() {
            return {
                values: [],
                products: [],
                product_quantity: [],
                expenses: {}
            }
        },
        computed: {
            dataReady() {
                if (typeof this.values[0] !== 'undefined') {
                    return true
                }
            }
        },
        created() {
            let url = '/api/report/home'
            axios.get(url)
                .then(res => {
                    this.values = this.sortDataByLabel(res.data)
                    this.expenses = res.data.expenses
                    console.log(this.expenses);
                    this.products[0] = res.data.products.product_name
                    this.products[1] = res.data.products.product_total
                    this.product_quantity[0] = res.data.product_quantity.product_quantity_name
                    this.product_quantity[1] = res.data.product_quantity.product_quantity_total
                })

        },
        methods: {
            sortDataByLabel(data) {
                let purchaseValue = []
                let salesValue = []
                let labels = []
                labels = _.union(data.purchases.purchase_dates.concat(data.sales.sale_dates))
                labels.map(date => {
                    //collect Purchase total according to date
                    if (_.includes(data.purchases.purchase_dates, date)) {
                        purchaseValue.push(data.purchases.purchase_totals[data.purchases.purchase_dates.indexOf(date)])
                    } else {
                        purchaseValue.push(0)
                    }

                    if (_.includes(data.sales.sale_dates, date)) {
                        salesValue.push(data.sales.sale_totals[data.sales.sale_dates.indexOf(date)])
                    } else {
                        salesValue.push(0)
                    }
                })

                return [labels, purchaseValue, salesValue]
            }
        }
    }

</script>
