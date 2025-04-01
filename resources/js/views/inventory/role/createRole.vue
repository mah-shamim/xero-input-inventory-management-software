<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-1">
                    <div class="formTitle ">
                        create roles
                        <Icon type="ios-people"></Icon>
                    </div>
                    <div class="formStyle">
                        <form @submit.prevent="postRole()" @keydown="forms.errors.clear($event.target.name)">
                            <input type="hidden" id="id" name="id" v-model="forms.id"/>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-element">
                                        <label><strong>Customer Name: *</strong></label>
                                        <input type="text" name="name" placeholder="Customer Name" tabindex="1"
                                               class="input-item" required="required"
                                               v-model="forms.name" @input="initialEmail">
                                        <p class="strong danger" v-text="forms.errors.get('name')"></p>
                                    </div>
                                    <div class="form-element">
                                        <label><strong>Phone Number : *</strong></label>
                                        <input type="text" name="phone" placeholder="Phone Number" tabindex="3"
                                               class="input-item" required="required" v-model="forms.phone">
                                        <p class="strong danger" v-text="forms.errors.get('phone')"></p>
                                    </div>
                                    <div class="form-element">
                                        <label><strong>Email *:</strong></label>
                                        <input type="email" name="email" placeholder="E-mail" tabindex="4"
                                               class="input-item" required="required" v-model="forms.email">
                                        <p class="strong danger" v-text="forms.errors.get('email')"></p>
                                        <div>
                                            <label>Set Default</label>
                                            <input type="checkbox" v-model="forms.is_default">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-element">
                                        <label>
                                            <strong>Address </strong>
                                        </label>
                                        <textarea placeholder="Enter your Address..."
                                                  rows="2"
                                                  name="address" class="input-item"
                                                  tabindex="2" v-model="forms.address"></textarea>
                                        <p class="strong danger" v-text="forms.errors.get('address')"></p>
                                    </div>
                                    <div class="form-element">
                                        <label>
                                            <strong>Details </strong>
                                        </label>
                                        <textarea placeholder="Enter Details..."
                                                  name="details"
                                                  rows="2"
                                                  class="input-item"
                                                  tabindex="2"
                                                  v-model="forms.details"></textarea>
                                        <p class="strong danger" v-text="forms.errors.get('details')"></p>
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

<script>
    export default {
        name: "create",
        data(){
            return {
                forms: this.$root.$data.forms,
            }
        },
        methods:{
            postRole(){

            }
        }
    }
</script>

<style scoped>

</style>