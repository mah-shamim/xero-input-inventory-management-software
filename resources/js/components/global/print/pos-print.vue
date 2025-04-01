<template>


    <div id="myPosPrint" v-if="isLoaded" class="pos-print">
        <div class="comTitle">{{companyDetails.name}}</div>
        <div class="pos-content">Address: {{companyDetails.address1}}</div>
        <template v-if="companyDetails.address2">
            <br>{{companyDetails.address2?companyDetails.address2:''}}
        </template>
        <div class="pos-content">
            Ph:
            <template v-for="(phone, index) in companyDetails.contact_phone">
                {{phone}}
                {{ companyDetails.contact_phone.length - 1 == index ? '' :',' }}
            </template>
        </div>

        <div>
            <div class="my-class">
                <b><u>Information</u></b>
            </div>
            <div v-for="key in clientData">
                <span> {{key.field}} :</span>
                <span> {{ key.data}}</span>
            </div>

        </div>
        <div class="my-class">
            <b><u>PRODUCT DETAIL</u></b>
        </div>
        <table class="ow-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>{{$store.state.quantity_label}}</th>
                <th>Unit Cost</th>
                <th>Total</th>
            </tr>

            <tr v-for="(product, index) in productDetails.products">
                <td>{{product.name}} <!--({{parseFloat(product.pivot.quantity).toFixed(2)}}) --></td>
                <td>{{product.quantityStr}}</td>
                <td>{{parseFloat(product.pivot.price).toFixed(2)}}</td>
                <td>{{parseFloat(product.pivot.subtotal).toFixed(2)}}</td>
            </tr>
            </thead>

            <tr>
                <td colspan="3" align="right"><b>Sub Total :</b></td>
                <td align="center">{{ normalTotal.toFixed(2) }}</td>
            </tr>

            <tr>
                <td colspan="3" align="right"><b>Overall Discount :</b></td>
                <td align="center">{{productDetails.overall_discount}}%</td>
            </tr>
            <tr>
                <td colspan="3" align="right"><b>{{$root.shippingLabel}} :</b></td>
                <td align="center">{{productDetails.shipping_cost}}</td>
            </tr>
            <tr>
                <td colspan="3" align="right"><b>Total :</b></td>
                <td align="center">{{parseFloat(productDetails.total).toFixed(2)}}</td>
            </tr>
            <tr>
                <td colspan="3" align="right"><b>Previous Due :</b></td>
                <td align="center">{{parseFloat(productDetails.previous_due).toFixed(2)}}</td>
            </tr>
            <tr>
                <td colspan="3" align="right"><b>Grand Total:</b></td>
                <td align="center">
                    {{(parseFloat(productDetails.total)+parseFloat(productDetails.previous_due)).toFixed(2)}}
                </td>
            </tr>


        </table>
        <template v-if="model !== 'Orders' && productDetails.payments.length>0">

            <div class="my-class"><u>PAYMENT DETAIL</u></div>

            <table class="ow-table">
                <thead>
                <tr class="bg-info-lt">
                    <td>Method</td>
                    <td>Date</td>
                    <td>Paid</td>
                </tr>
                </thead>
                <tbody>

                <tr v-for="(payment, index) in productDetails.payments">
                    <td v-if="payment.payment_type == 1">Cash</td>
                    <td v-if="payment.payment_type == 2">Credit Card</td>
                    <td v-if="payment.payment_type == 3">Cheque</td>
                    <td>{{payment.created_at}}</td>
                    <td>{{parseFloat(payment.paid).toFixed(2)}}</td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        {{$root.$data.erp.report.checkPaymentStatus(productDetails.total,
                        productDetails.payments)-parseFloat(productDetails.previous_due)<0?'Due':$root.$data.erp.report.checkPaymentStatus(productDetails.total,
                        productDetails.payments)-parseFloat(productDetails.previous_due)==0?'Due':$root.$data.erp.report.checkPaymentStatus(productDetails.total,
                        productDetails.payments)-parseFloat(productDetails.previous_due)>0?'Adjustment Paid':''}}
                    </td>
                    <td>
                        {{(parseFloat($root.$data.erp.report.checkPaymentStatus(productDetails.total,productDetails.payments))-parseFloat(productDetails.previous_due)).toFixed(2)}}
                    </td>
                </tr>


                </tbody>
            </table>
        </template>

        <h4 v-if="returnDetails.length>0"><u>RETURN DETAILS</u></h4><br>
        <table v-if="returnDetails.length>0" class="ow-table">
            <thead>
            <tr class="bg-info-lt">
                <td align="center">Name</td>
                <td align="center">{{$store.state.quantity_label}}</td>
                <td align="center">Unit</td>
                <td align="center">Amount</td>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) in returnDetails">
                <td align="center">{{item.product.name}}</td>
                <td align="center">{{item.quantity}}</td>
                <td align="center">{{item.unit.key}}</td>
                <td align="center">{{item.amount}}</td>
            </tr>
            </tbody>
        </table>
        <p class="my-class" align="center"> --- Thanks for your shopping ---</p>
        <p class="my-class" align="center" style="visibility: hidden">. </p>
    </div>
</template>


<script>
    export default {
        data() {
            return {
                specialRequirement: {},
                user: auth
            }
        },
        props: {

            clientData: {
                type: Array,
                default: () => []
            },
            information: {
                type: Array,
                default: () => []
            },
            model: {
                type: String,
                default: () => ''
            },
            productDetails: {
                type: Object,
                default: () => {
                }
            },
            companyDetails: {
                type: Object,
                default: () => {
                }
            },
            returnDetails: {
                type: Array,
                default: () => []
            }


        },
        computed: {
            isLoaded() {
                return !_.isEmpty(this.productDetails)
            },
            normalTotal() {

                let withoutDiscountAmount = this.productDetails.overall_discount > 0.0 ? this.productDetails.total * 100 / (100 - this.productDetails.overall_discount) : this.productDetails.total

                return withoutDiscountAmount - this.productDetails.shipping_cost

            }
        }


    }
</script>
<style scoped>
    .pos-print {
        width: 400px /* normal width */
    }

    @media print {
        .pos-print {
            width: 100% /* print width */
        }
    }
</style>



