<template>
    <div class="container">
        <div class="row">
            <div class="col-1">
                <h5 class="formTitle">{{title.text}} PURCHASE Order -
                    <Icon type="cube"> </Icon>
                </h5>
                <div class="formStyle">
                    <form @submit.prevent="postPurchase"
                          @keydown="forms.errors.clear($event.target.name)"
                          v-if="isLoaded">
                        <br>
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
                                        <strong>{{$root.shippingLabel}}</strong>
                                    </label>
                                    <input type="number" name="shipping_cost" class="input-item"
                                           :placeholder="$root.shippingLabel" v-model="forms.shipping_cost" tabindex="8"
                                           step="any" min="0">
                                    <p class="strong danger" v-text="forms.errors.get('shipping_cost')"></p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-element">
                                    <label>
                                        <strong>Select Suppliers :</strong>
                                    </label>
                                    <Select v-model="forms.supplier_id" filterable
                                            class="alignment">
                                        <Option :value="supplier.id" v-for="supplier in suppliers" :key="supplier.id"
                                                :label="supplier.company">
                                            <span>{{supplier.company}}</span>
                                            <span style="float:right;color:#ccc">{{supplier.user.name}}</span>
                                        </Option>
                                    </Select>
                                    <p class="strong danger" v-text="forms.errors.get('supplier_id')"></p>
                                </div>
                                <div class="form-element">
                                    <label>
                                        <strong>Overall Discount: (%) </strong>
                                    </label>
                                    <input type="number" step="any" class="input-item" placeholder="Rate"
                                           v-model="forms.overall_discount" tabindex="7" min="0">
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
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-element inline">
                                <label>
                                    <span class="dark strong">Product : </span>
                                </label>
                                <Select
                                        v-model="product_id"
                                        filterable
                                        tabindex="3"
                                        clearable
                                        remote
                                        :remote-method="getProducts"
                                        @on-change="addProduct"
                                        :disabled="method==='Edit'"
                                        :loading="loadingMessage"
                                        :placeholder="'please enter product name'"
                                        :loadingText="'loading....'"
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
                                        <div class="col-1 pd-0">{{$store.state.quantity_label}}</div>
                                        <div class="col-1 pd-0">Price</div>
                                        <div class="col-1 pd-0">Discount</div>
                                    </div>
                                </td>
                                <td class="pd-2">Total</td>
                                <td class="pd-2">action</td>
                            </tr>
                            <tr v-for="(item, index) in items">
                                <td>{{index+1}}</td>
                                <td class="bg-default">
                                    {{item.pname}}
                                </td>
                                <td>
                                    <Select v-model="item.warehouse" filterable placeholder="Select Warehouse"
                                            class="input-item">
                                        <Option v-for="item in warehouses" :value="item.id" :key="item.id">
                                            <span>{{ item.name }}</span>
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
                                    <!--<a :id="tooltipId + index" title="Unit is not selected yet"
                                       class="hover top bg-info">
                                        <Icon type="arrow-swap" class="barIcon"></Icon>
                                    </a>-->
                                    <conversion-component :componentId="tooltipId + index"
                                                          :baseUnit=parseInt(item.baseUnit)
                                                          :toUnit=parseInt(item.unit)
                                                          :quantity=parseFloat(item.quantity)
                                                          :units="item.units"
                                                          :isPurchase="true"
                                                          :productId="item.product_id">
                                    </conversion-component>
                                </td>
                                <td>
                                    <div class="form-element inline">
                                        <input type="number" v-model="item.quantity" class="input-item"
                                               :placeholder="$store.state.quantity_label" min="0" step="any"/>
                                        <input type="text" v-model="parseFloat(item.unit_price).toFixed(4)"
                                               class="input-item">
                                        <input type="number" step="any" v-model="item.discount" class="input-item"
                                               placeholder="Discount" min="0"/>
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
                                <td class="text-right dark" colspan="5">Pay:</td>
                                <td><input type="number" v-model="forms.paid" class="input-item bg-white"
                                           @keyup="calDue" step="any" min="0"></td>
                                <td>
                                    <select name="payment_type" v-model="payment_type">
                                        <option value="1" selected>cash</option>
                                        <option value="2">credit card</option>
                                        <option value="3">cheque</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right" colspan="5">Due:</td>
                                <td>{{due}}</td>
                                <td :class="`${payment_status==1?'bg-success':payment_status==2?'bg-warning':'bg-danger'}`">
                                    {{payment_status==1?'paid': payment_status==2 ? 'partial':'due'}}
                                </td>
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
                                <button class="dark m">{{button.text}}</button>
                                <button type="button" class="btn warning m" @click="onCancel">Cancel</button>
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