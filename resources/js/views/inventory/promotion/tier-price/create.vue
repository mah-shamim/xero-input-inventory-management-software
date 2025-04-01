<template>
    <div class="row" v-if="isLoaded">
        <div class="col-1">
            <h5 class="formTitle">{{title.text}} Tier Pricing -
                <Icon type="ios-plus"></Icon>
            </h5>
            <div class="formStyle">
                <form @submit.prevent="postTierPrice" @keydown="forms.errors.clear($event.target.name)">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-element">
                                <input type="hidden" id="id" name="id" v-model="forms.id"/>
                                <label for=""> Product </label>
                                <Select v-model="forms.product_id"
                                        filterable
                                        tabindex="3"
                                        clearable
                                        remote
                                        :remote-method="getProducts"
                                        :loading="loading"
                                        :placeholder="'Please enter product name'"
                                        :loadingText="'loading....'">
                                    <Option v-for="product in products" :value="product.id" :key="product.id"
                                            name="product_id">
                                        <span>{{ product.name }}</span>
                                        <span style="float:right">{{product.code}}</span>
                                    </Option>
                                </Select>
                                <p class="strong danger" v-text="forms.errors.get('product_id')"></p>
                            </div>
                            <div class="form-element">
                                <label for="">
                                    Price:
                                </label>
                                <input type="number" class="input-item" placeholder="Price" required
                                       name="price"
                                       v-model="forms.price" tabindex="4" step="any" min="0">
                                <p class="strong danger" v-text="forms.errors.get('price')"></p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-element">
                                <label for=""> Price Category </label>
                                <Select v-model="forms.price_category_id" filterable
                                        placeholder="Select Price Category">
                                    <Option v-for="priceCategory in priceCategories" :value="priceCategory.id"
                                            :key="priceCategory.id"
                                            name="price_category_id">
                                        <span>{{ priceCategory.name }}</span>
                                    </Option>
                                </Select>
                                <p class="strong danger" v-text="forms.errors.get('price_category_id')"></p>
                            </div>

                            <div class="form-element">

                                <label>
                                    <strong>Starting Date:</strong>
                                </label>
                                <br>
                                <DatePicker type="datetime"
                                            placeholder="Select date and time"
                                            v-model="forms.start_date"
                                            style="width: 100%"
                                            format="dd-MM-yyyy HH:mm:ss"
                                >
                                </DatePicker>

                                <p class="strong danger" v-text="forms.errors.get('start_date')"></p>
                            </div>
                        </div>
                        <div class="col-4">

                            <div class="form-element">
                                <label for="">
                                    Quantity:
                                </label>
                                <input type="number" class="input-item" placeholder="Quantity" required name="quantity"
                                       v-model="forms.quantity" tabindex="4" step="any" min="0">
                                <p class="strong danger" v-text="forms.errors.get('quantity')"></p>
                            </div>
                            <div class="form-element">

                                <label>
                                    <strong>Ending Date:</strong>
                                </label>
                                <br>
                                <DatePicker type="datetime"
                                            placeholder="Select date and time"
                                            v-model="forms.end_date"
                                            style="width: 100%"
                                            format="dd-MM-yyyy HH:mm:ss"
                                >
                                </DatePicker>

                                <p class="strong danger" v-text="forms.errors.get('end_date')"></p>
                            </div>

                        </div>
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

<script src="./js/create.js"></script>

<style>

</style>