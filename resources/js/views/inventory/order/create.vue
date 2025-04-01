<template>

    <!----------Start Customer Modal--------->
    <div class="container">



        <div id="customercreate" class="modal large">
            <div class="modal-content">
                <div class="modal-header bg-primary">

                    <button class="close"></button>
                    <br>
                </div>
                <div class="modal-body">
                    <customer-create
                            @customerDetails="getNewCustomer">
                    </customer-create>
                </div>

            </div>
        </div>

        <!---------End Customer Modal---------->

        <div class="row">
            <div class="col-6">
                <h5 class="formTitle">{{title.text}} Sales Order -
                    <Icon type="cube"></Icon>
                </h5>
                <div class="formStyle">
                    <form @submit.prevent="postOrders" @keydown="forms.errors.clear($event.target.name)"
                          v-if="isLoaded">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-element">
                                    <label>

                                        <strong>Date of Order:</strong>
                                    </label>
                                    <br>
                                    <DatePicker type="datetime"
                                                placeholder="Select date and time"
                                                v-model="forms.order_date"
                                                style="width: 100%"
                                                format="dd-MM-yyyy HH:mm:ss"
                                    >
                                    </DatePicker>

                                    <p class="strong danger" v-text="forms.errors.get('order_date')"></p>
                                </div>
                                <div class="form-element">
                                    <label>
                                        <strong>Shipping Cost:</strong>
                                    </label>
                                    <input type="number" name="shipping_cost" class="input-item"
                                           placeholder="Shipping Cost" v-model="forms.shipping_cost"
                                           tabindex="8" step="any" min="0">
                                    <p class="strong danger" v-text="forms.errors.get('shipping_cost')"></p>
                                </div>

                            </div>
                            <div class="col-4">
                                <div class="form-element" style="position:relative;">
                                    <label>
                                        <strong>Select Customer :</strong>
                                    </label>
                                    <Select class="cusCaret" v-model="forms.customer_id" filterable>
                                        <Option :value="customer.id" v-for="customer in customers" :key="customer.id"
                                                :label="customer.user.name">
                                            <span>{{customer.user.name}}</span>
                                            <span style="float:right;color:#ccc">{{customer.phone}}</span>
                                        </Option>
                                        <Button slot="append" icon="ios-search"></Button>
                                    </Select>
                                    <p class="strong danger" v-text="forms.errors.get('customer_id')"></p>
                                    <button onclick="event.preventDefault();"  data-target="customercreate"
                                            class="catch-modal info m barIcon ivu-icon ivu-icon-person-add" id="cusCreate">

                                    </button>
                                </div>

                                <div class="form-element">
                                    <label>
                                        <strong>Overall Discount: (%)</strong>
                                    </label>
                                    <input type="number" name="overall_discount" class="input-item" placeholder="Rate"
                                           v-model="forms.overall_discount" tabindex="7" step="any" min="0">

                                    <p class="strong danger" v-text="forms.errors.get('overall_discount')"></p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-element">
                                    <label>
                                        <strong> Status *:</strong>
                                    </label>
                                    <select name="status" v-model="forms.status" class="input-item"
                                            tabindex="9">
                                        <option value="1">New</option>
                                        <option value="2">Processing</option>
                                        <option value="3">Completed</option>
                                    </select>
                                    <p class="strong danger" v-text="forms.errors.get('status')"></p>
                                </div>

                                <div class="form-element">
                                    <label>
                                        <strong>Biller *:</strong>
                                    </label>
                                    <input type="text" name="biller" class="input-item" placeholder="Biller Name"
                                           v-model="forms.biller=orders.biller" tabindex="1" required="required">
                                    <p class="strong danger" v-text="forms.errors.get('biller')"></p>
                                </div>

                            </div>

                        </div>


                        <div class="row">
                            <div class="form-element inline">
                                <label>
                                    <strong>Product :</strong>
                                </label>
                                <Select
                                        v-model="product_id"
                                        filterable
                                        tabindex="3"
                                        clearable
                                        remote
                                        :remote-method="getProducts"
                                        :loading="loading"
                                        :placeholder="'please enter product name'"
                                        :loadingText="'loading....'"
                                        @on-change="addProduct"
                                        :disabled="method==='Edit'"
                                >
                                    <Option
                                            :value="product.id"
                                            v-for="(product, index) in products"
                                            :key="product.id"
                                    >
                                        <span>{{product.name}}</span>
                                        <span style="float:right;color:#ccc">{{product.code}}</span>
                                    </Option>
                                </Select>
                            </div>
                        </div>
                        <hr>
                        <p class="strong danger" v-text="forms.errors.get('items')"></p>
                        <table class="table stripe">
                            <tbody>
                            <tr v-if="items.length>0" class="bg-default">
                                <td class="pd-2">#</td>
                                <td class="pd-2">Product Name</td>
                                <td class="pd-2">Warehouse</td>
                                <td class="pd-2">Unit</td>
                                <td class="pd-0 bg-info">
                                    <div class="row mrb-0">
                                        <div class="col-1 pd-0">Quantity</div>
                                        <div class="col-1 pd-0">Price</div>
                                        <div class="col-1 pd-0">Discount</div>
                                    </div>
                                </td>
                                <td class="pd-2">Total</td>
                                <td class="pd-2">action</td>
                            </tr>
                            <tr v-for="(item, index) in items">
                                <td>{{index + 1}}</td>
                                <td class="bg-default">
                                    {{item.pname}}
                                </td>
                                <td>
                                    <Select v-model="item.warehouse" filterable
                                            placeholder="Select Warehouse"
                                            class="input-item">
                                        <Option v-for="item in item.warehouses" :value="item.id" :key="item.id">
                                            <span> {{ item.name }} </span>
                                        </Option>
                                    </Select>
                                </td>
                                <td>
                                    <Select v-model="item.unit" filterable placeholder="Select Unit"
                                            @on-change="getPrice(item.unit, index)">
                                        <Option v-for="item in item.units" :value="item.id" :key="item.id">
                                            <span style="text-align: center;">{{ item.key }}</span>
                                        </Option>
                                    </Select>
                                    <conversion-component :componentId="tooltipId + index"
                                                          :baseUnit="parseInt(item.baseUnit)"
                                                          :toUnit="item.unit"
                                                          :quantity="parseFloat(item.quantity)"
                                                          :units="item.units">
                                    </conversion-component>
                                </td>
                                <td>
                                    <div class="form-element inline">
                                        <input type="number" v-model="item.quantity"
                                               class="input-item"
                                               placeholder="Quantity" step="any" min="0"/>
                                        <span class="badge bg-info proStock">
                                            {{item.productInStock}}
                                        </span>


                                        <input type="text" v-model="parseFloat(item.unit_price).toFixed(4)"
                                               class="input-item">
                                        <input type="number" v-model="item.discount" class="input-item"
                                               placeholder="Discount" step="any" min="0"/>
                                    </div>
                                </td>
                                <td class="bg-warning">
                                    {{getTotalPrice(item)}}
                                </td>
                                <td class="bg-default">
                                    <a class="button danger s" @click="removeProduct(index)">Remove</a>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot class="bg-primary" v-if="items.length>0">
                            <tr>
                                <td>{{this.items.length}}</td>
                                <td class="text-right" colspan="4">Grand Total:</td>
                                <td>{{total}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="dark">Order note:</td>
                                <td class="bg-white" colspan="2"><textarea name="note" v-model="forms.note"
                                                                           class="input-item" cols="30"
                                                                           rows="10"></textarea></td>
                            </tr>
                            </tfoot>
                        </table>


                        <div class="row" v-if="items.length>0">
                            <div class="col-1">
                                <button type="button" class="btn warning m" @click="onCancel">Cancel</button>
                                <button class="dark m">{{button.text}}</button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

</template>

<script src="./js/create.js"></script>

<style>
    .table > tbody > tr > td {
        padding: 5px 5px !important;
        line-height: 1.8;
    }

    .form-element.inline {
        margin: 0 !important;
    }

    table a.button.s {
        padding: 0px 5px;
        margin: 0px;
    }

    table .ivu-select-selection {
        background-color: transparent;
        border-radius: 0;
        border: none;
    }
</style>