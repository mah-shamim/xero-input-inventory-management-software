<template>
    <div class="content-area">
        <div class="heading">
            <div class="reportHandle">
                <div class="int-title">Purchase Report</div>
                <div>
                    <label>
                        &nbsp;<span class="dark">Total:</span>
                    </label>
                    <select v-model="totalPageNumber" class="with-border">
                        <option value=10>10</option>
                        <option value=20>20</option>
                        <option value=30>30</option>
                        <option value=40>40</option>
                        <option value=50>50</option>
                        <option value=60>60</option>
                        <option value=70>70</option>
                        <option value=80>80</option>
                        <option value=90>90</option>
                        <option value=100>100</option>
                    </select>
                </div>
            </div>
        </div>
        <a href="#" @click.prevent="printMe" class="printAll">
            Print
        </a><br>
        <div class="query-area">
            <div class="col-1">
                <DatePicker type="datetimerange" placeholder="Select date and time" v-model="purchase_date"
                            style="width: 100%"></DatePicker>
            </div>
            <div class="col-1">
                <input type="text" v-model="ref" class="input-item" placeholder="reference number" debounce="2000">
            </div>
            <div class="col-1">
                <input type="text" v-model="name" class="input-item" placeholder="supplier's company">
            </div>
            <div class="col-1">
                <input type="text" class="input-item" placeholder="product name" v-model="products">
            </div>
        </div>
        <div class="row pdt-10" id="printArea">
            <table class="table withLast">
                <thead>
                <tr>
                    <td>#</td>
                    <td>ref(date)</td>
                    <td>Supplier</td>
                    <td>Product List</td>
                    <td>Transactions type</td>
                    <td>Quantity</td>
                    <td>Quantity (Base Unit)</td>
                    <td>Price</td>
                    <td>Discount</td>
                    <td>overall Discount</td>
                    <td>Debit</td>
                    <td>Credit</td>
                    <td>status</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(purchase, index) in purchases">
                    <td>{{index + 1}}</td>
                    <td>
                        <router-link :to="{name: 'purchasesShow', params:{id:purchase.id}}">
                            {{purchase.ref}}({{$root.timeFormat(purchase.purchase_date)}})
                        </router-link>
                    </td>
                    <td>
                        {{purchase.supplier.company}}
                    </td>
                    <td>
                        <ul class="table-list">
                            <li v-for="(product, index) in purchase.products" class="pd-0">
                                {{index+1}}.
                                {{product.name}}
                            </li>
                        </ul>
                    </td>
                    <td>
                        <div v-for="payment in purchase.payments">
                            {{$root.paymentMethods(payment.payment_type)}}
                        </div>
                    </td>
                    <td>
                        <ul class="table-list">
                            <li v-for="(product, index) in purchase.products" class="pd-0">
                                {{product.pivot.purchase_quantity}} {{product.unit_name}}
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="table-list">
                            <li v-for="(product, index) in purchase.products" class="pd-0">
                                {{parseFloat(product.quantityBaseUnit).toFixed(2)}} ({{product.unit.key}})
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="table-list">
                            <li v-for="(product, index) in purchase.products" class="pd-0">
                                {{parseFloat(product.pivot.price)}}
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="table-list">
                            <li v-for="(product, index) in purchase.products" class="pd-0">
                                {{product.pivot.discount}}
                            </li>
                        </ul>
                    </td>
                    <td>{{purchase.overall_discount}}</td>
                    <td>
                        {{purchase.total}}
                    </td>
                    <td>
                        <div v-for="payment in purchase.payments">
                            {{payment.paid}}
                        </div>
                    </td>
                    <td>
                        {{$root.$data.erp.report.checkPaymentStatus(purchase.total,purchase.payments)}}
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="10" class="text-right">total</td>
                    <td>{{totalPurchase[0]}}</td>
                    <td>{{totalPurchase[1]}}</td>
                    <td>{{totalPurchase[2]}}</td>
                </tr>
                <tr>
                    <td colspan="13">
                        <pagination class="right"
                                :data="laravelData"
                                :limit="5"
                                v-on:pagination-change-page="getResults"></pagination>
                    </td>
                </tr>
                </tfoot>
            </table>

        </div>
    </div>
</template>

<script src="./actualPurchaseReport.js"></script>

<style>

</style>