<template>
    <div id="myPrint" v-if="isLoaded">
        <!--&lt;!&ndash;<h2 align="center" border="2"> {{model}} Ledger</h2>&ndash;&gt;-->
        <!--<hr>-->
        <h1 align="center">{{companyDetails.name}} </h1>
        <p align="center">
            <span align="center" style="padding-top: 0; padding-bottom: 0">Address: {{companyDetails.address1}}</span> <br>
                {{companyDetails.address2?companyDetails.address2:''}}
            <span align="center" style="padding-top: 0; padding-bottom: 0">Phone number:
            <template v-for="(phone, index) in companyDetails.contact_phone">
                {{phone}}{{ companyDetails.contact_phone.length - 1 == index ? '' :',' }}
            </template>
        </span>
        </p>
        <table style="border-top:1px dashed lightgrey;">
            <tr>
                <td width="400px">
                    <p><b><u>Customer Information</u></b></p>
                    <table width="100%">
                        <tr v-for="key in clientData">
                            <td style="width: 90px;">
                                <b>{{key.field}} :</b>
                            </td>
                            <td>
                                <span>{{ key.data}}</span>
                            </td>
                        </tr>

                    </table>
                </td>
                <td width="400px" align="right"><br><br>
                    <h4><b><u>{{model}} Information</u></b></h4>
                    <table width="100%">
                        <tr v-for="key in information">
                            <td align="right">
                                <b>{{key.field}}: </b>
                            </td>
                            <td align="right" style="width:180px;">

                                <span v-if="key.field=='Date'">{{$root.timeFormat( key.data)}}</span>
                                <span v-else>{{ key.data}}</span>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <br>
                    <!--<h4><b><u>SHOP INFORMATION</u></b></h4><br>-->
                    <table align="right">
                        <thead>
                        <tr>
                            <td align="right">

                            </td>

                        </tr>
                        </thead>
                    </table>
                </td>
            </tr>
        </table>

        <br>
        <h4><u>PRODUCT DETAIL</u></h4>
        <table class="table" align="center">
            <thead>
            <tr>
                <td align="center">Sl No</td>
                <!--<td align="center">Code</td>-->
                <td align="center">Name</td>
                <td align="center">{{$store.state.quantity_label}}</td>
                <td align="center">Unit Cost</td>
                <td align="center">Discount </td>
                <td align="center">Total</td>
                <td align="center">Status</td>
            </tr>

            <tr v-for="(product, index) in productDetails.products">
                <td align="center">{{index+1}}</td>
                <!--<td align="center">{{product.code}}</td>-->
                <td align="center">{{product.name}} <!--({{parseFloat(product.pivot.quantity).toFixed(2)}}) --></td>
                <td align="center">{{product.quantityStr}}
                    <template v-if="product.pivot.quantity">
                        ({{parseFloat(product.pivot.quantity).toFixed(4)}})
                    </template>

                </td>
                <td align="center">{{product.pivot.price}}</td>
                <td align="center" >{{product.pivot.discount}}</td>
                <td align="center">{{product.pivot.subtotal}}</td>
                <td align="center" v-if="productDetails.status == 1">pending</td>
                <td align="center" v-if="productDetails.status == 2">paid</td>
                <td align="center" v-if="productDetails.status == 3">partial</td>
            </tr>
            </thead>

            <tr>
                <td colspan="5" align="right"><b>Sub Total :</b></td>
                <td colspan="2" align="center">{{ normalTotal.toFixed(4) }}</td>
            </tr>

            <tr>
                <td colspan="5" align="right" ><b>Overall Discount :</b></td>
                <td colspan="2" align="center">{{productDetails.overall_discount}}%</td>
            </tr>
            <tr>
                <td colspan="5" align="right"><b>{{$root.shippingLabel}} :</b></td>
                <td colspan="2" align="center">{{productDetails.shipping_cost}}</td>
            </tr>
            <tr>
                <td colspan="5" align="right"><b>Total :</b></td>
                <td colspan="2" align="center">{{productDetails.total}}</td>
            </tr>
            <tr v-if="productDetails.hasOwnProperty('previous_due')">
                <td colspan="5" align="right"><b>Previous Due :</b></td>
                <td colspan="2" align="center">{{productDetails.previous_due}}</td>
            </tr>
            <tr v-if="productDetails.hasOwnProperty('previous_due')">
                <td colspan="5" align="right"><b>Grand Total:</b></td>
                <td colspan="2" align="center">{{(parseFloat(productDetails.total)+parseFloat(productDetails.previous_due)).toFixed(4)}}</td>
            </tr>

        </table>

        <br><br>

        <h4 v-if="model!=='Orders'"><u>PAYMENT DETAIL</u></h4><br>
        <table class="table" width="100%" v-if="model!=='Orders'">
            <thead>
            <tr class="bg-info-lt">
                <td align="center">SL</td>
                <td align="center">Method</td>
                <td align="center">Date</td>
                <td align="center">Paid</td>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(payment, index) in productDetails.payments">
                <td align="center">{{index +1 }}</td>
                <td align="center" v-if="payment.payment_type == 1">Cash</td>
                <td align="center" v-if="payment.payment_type == 2">Credit Card</td>
                <td align="center" v-if="payment.payment_type == 3">Cheque</td>
                <td align="center">{{$root.timeFormat(payment.created_at)}}</td>
                <td align="center">{{payment.paid}}</td>
            </tr>
            <tr v-if="productDetails.hasOwnProperty('previous_due')">
                <td colspan="3" align="right">
                    {{$root.$data.erp.report.checkPaymentStatus(productDetails.total, productDetails.payments)-parseFloat(productDetails.previous_due)<0?'Due':
                    $root.$data.erp.report.checkPaymentStatus(productDetails.total, productDetails.payments)-parseFloat(productDetails.previous_due)==0?'Due':
                        $root.$data.erp.report.checkPaymentStatus(productDetails.total, productDetails.payments)-parseFloat(productDetails.previous_due)>0?'Adjustment Paid':''}}
                </td>
                <td align="center">{{($root.$data.erp.report.checkPaymentStatus(productDetails.total,productDetails.payments)-parseFloat(productDetails.previous_due)).toFixed(4)}}</td>
            </tr>

            <tr v-else>
                <td colspan="3" align="right">
                    {{$root.$data.erp.report.checkPaymentStatus(productDetails.total, productDetails.payments)<0?'Due':
                    $root.$data.erp.report.checkPaymentStatus(productDetails.total, productDetails.payments)==0?'Due':
                        $root.$data.erp.report.checkPaymentStatus(productDetails.total, productDetails.payments)>0?'Adjustment Paid':''}}
                </td>
                <td align="center">{{parseFloat($root.$data.erp.report.checkPaymentStatus(productDetails.total,productDetails.payments)).toFixed(4)}}</td>
            </tr>

            </tbody>
        </table>
        <br><br>
       <div v-if="model!=='Orders'">
           <h4 v-if="returnDetails.length>0"><u>RETURN  DETAILS</u></h4><br>
           <table v-if="returnDetails.length>0" class="table" width="100%">
               <thead>
               <tr class="bg-info-lt">
                   <td align="center">SL</td>
                   <td align="center">Code</td>
                   <td align="center">Name</td>
                   <td align="center">{{$store.state.quantity_label}}</td>
                   <td align="center">Unit</td>
                   <td align="center">Amount</td>
               </tr>
               </thead>
               <tbody>
               <tr v-for="(item, index) in returnDetails">
                   <td align="center">{{index +1 }}</td>
                   <td align="center">{{item.product.code}}</td>
                   <td align="center">{{item.product.name}}</td>
                   <td align="center">{{item.quantity}}</td>
                   <td align="center">{{item.unit.key}}</td>
                   <td align="center">{{item.amount}}</td>
               </tr>
               </tbody>
           </table>

           <table width="90%">
               <tbody>
               <tr>
                   <td align="right" >
                       <br><br><br>
                       <span>-----------------------------------</span>
                       <h3>Authorized Signature</h3>
                   </td>
               </tr>
               </tbody>
           </table>
       </div>

    </div>
</template>


<script>
    export default {
        data(){
            return {
                specialRequirement:{

                },
                user:auth
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
            returnDetails:{
                type: Array,
                default:()=>[]
            }


        },
        computed: {
            isLoaded() {
                return !_.isEmpty(this.productDetails)
            },
            normalTotal(){

                let withoutDiscountAmount = this.productDetails.overall_discount > 0.0 ? this.productDetails.total * 100 /(100 - this.productDetails.overall_discount) : this.productDetails.total

                return withoutDiscountAmount - this.productDetails.shipping_cost

            }
        }


    }
</script>



