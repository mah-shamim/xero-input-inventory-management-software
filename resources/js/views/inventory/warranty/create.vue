<template>

    <div class="row" v-if="isLoaded">
        <div class="col-1">
            <h5 class="formTitle">{{title.text}} Warranty -
                <Icon type="ios-plus"></Icon>
            </h5>
            <div class="formStyle">
                <form @submit.prevent="postWarranty" @keydown="forms.errors.clear($event.target.name)">
                    <div class="row">
                        <div class="col-1">
                            <div class="form-element">
                                <input type="hidden" id="id" name="id" v-model="forms.id"/>
                                <label>
                                    <strong>Date of Warranty:</strong>
                                </label>
                                <br>
                                <DatePicker type="datetime"
                                            placeholder="Select date and time"
                                            v-model="forms.warranty_date"
                                            style="width: 100%"
                                            format="dd-MM-yyyy HH:mm:ss"
                                >
                                </DatePicker>

                                <p class="strong danger" v-text="forms.errors.get('warranty_date')"></p>
                            </div>

                            <div class="form-element">
                                <label for=""> Customer </label>
                                <Select v-model="forms.customer_id" filterable
                                        placeholder="Select Customer">
                                    <Option v-for="customer in customers" :value="customer.id" :key="customer.id"
                                            name="customer_id">
                                        <span>{{ customer.user?customer.user.name:'' }}</span>
                                    </Option>
                                </Select>
                                <p class="strong danger" v-text="forms.errors.get('customer_id')"></p>
                            </div>

                        </div>
                        <div class="col-1">
                            <div class="form-element">
                                <label for=""> Product </label>
                                <Select v-model="forms.product_id"
                                        clearable
                                        filterable
                                        remote
                                        :disabled="method==='Edit'"
                                        :remote-method="getProducts"
                                        :loading="loadingMessage"
                                        :placeholder="'Please enter product name'"
                                        :loadingText="'loading....'"
                                        :class="{'warranty-product':method==='Edit'}"

                                        >
                                    <Option v-for="product in products"
                                            :value="product.id"
                                            :key="product.id"
                                            name="product_id">
                                        <span>{{ product.name }}</span>
                                    </Option>
                                </Select>
                                <p class="strong danger" v-text="forms.errors.get('product_id')"></p>
                            </div>
                            <div class="form-element">
                                <label for=""> Status </label>
                                <Select v-model="forms.status" filterable
                                        placeholder="Select Status">
                                    <Option v-for="(status,index) in statuses" :value="status" :key="index"
                                            name="status">
                                        <span>{{ status }}</span>
                                    </Option>
                                </Select>
                                <p class="strong danger" v-text="forms.errors.get('status')"></p>
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-element">
                                <label for="">
                                    Quantity:
                                </label>
                                <input type="text" class="input-item" placeholder="Quantity" required name="quantity"
                                       id="" v-model="forms.quantity" tabindex="2">
                                <p class="strong danger" v-text="forms.errors.get('quantity')"></p>
                            </div>

                        </div>
                    </div>
                    <div class="form-element" style="padding: 0 10px">
                        <label for="">
                            Note
                        </label>
                        <textarea class="input-item" name="note" cols="30" rows="10"
                                  v-model="forms.note"
                                  tabindex="6"></textarea>
                        <p class="strong danger" v-text="forms.errors.get('note')"></p>
                    </div>
                    <div class="row">
                        <div class="col-1">
                            <button type="submit" class="btn dark m" tabindex="7">{{button.text}}</button>
                            <button type="button" class="btn warning m" @click="onCancel">{{button.cancel}}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>
<style lang="scss">
    .warranty-product{
        input{
            color: #f1efef!important;
        }
    }
</style>
<script src="./js/create.js"></script>