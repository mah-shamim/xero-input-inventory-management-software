<template>
    <div>
        <div class="container" v-if="isLoaded">
            <div class="row">
                <div class="col-1">
                    <h5 class="formTitle">{{ title.text }} PRODUCT DAMAGE -
                        <Icon type="ios-people"></Icon>
                    </h5>
                    <div class="formStyle">
                        <form @submit.prevent="postProductDamage" @keydown="forms.errors.clear($event.target.name)">
                            <input type="hidden" id="id" name="id" v-model="forms.id"/>
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-element">
                                        <label>
                                            <strong>Warehouse *</strong>
                                        </label>

                                        <Select v-model="forms.warehouse_id"
                                                @on-change="getProducts" filterable>
                                            <Option v-for="item in warehouses" :value="item.id" :key="item.id">{{
                                                item.name }}
                                            </Option>
                                        </Select>

                                        <p class="strong danger" v-text="forms.errors.get('warehouse_id')"></p>

                                    </div>
                                    <div class="form-element">
                                        <label>
                                            <strong>Sale Value *</strong>
                                        </label>

                                        <input type="number" name="sale_value"
                                               v-model="forms.sale_value"
                                               class="input-item" placeholder="Sale Value" min="0" step="any" required>
                                        <p class="strong danger" v-text="forms.errors.get('sale_value')"></p>


                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-element">
                                        <label>
                                            <strong>Product *</strong>
                                        </label>

                                        <Select v-model="forms.product_id" @on-change="getUnits"
                                                filterable>
                                            <Option v-for="item in products" :value="item.id" :key="item.id">
                                                <span>{{item.name}}</span>
                                                <span style="float:right;color:#ccc">{{item.code}}</span>
                                            </Option>
                                        </Select>

                                        <p class="strong danger" v-text="forms.errors.get('product_id')"></p>

                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-element">
                                        <label>
                                            <strong>Quantity *</strong>
                                        </label>

                                        <input type="number" v-model="forms.quantity" class="input-item"
                                               placeholder="Quantity" min="0" step="any" required/>
                                        <p class="strong danger" v-text="forms.errors.get('quantity')"></p>

                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-element">
                                        <label>
                                            <strong>Unit *</strong>
                                        </label>

                                        <Select v-model="forms.unit_id" filterable>
                                            <Option v-for="item in units" :value="item.id" :key="item.id">
                                                <span>{{item.key}}</span>
                                            </Option>
                                        </Select>

                                        <p class="strong danger" v-text="forms.errors.get('product_id')"></p>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <div class="form-element inline">
                                        <button type="submit" class="info m">{{button.text}}</button>
                                        <button type="button" class="btn warning m" @click="onCancel">
                                            {{button.cancel}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script src="./js/create.js"></script>