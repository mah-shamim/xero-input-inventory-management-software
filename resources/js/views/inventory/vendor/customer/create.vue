<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-1">
                    <h5 class="formTitle">
                        Create Customer <Icon type="ios-people"></Icon>
                    </h5>
                    <div class="formStyle">
                        <form @submit.prevent="postCustomer">
                            <div class="row">
                                <div class="col-1">
                                    <label for="company_name">Company Name</label>
                                    <input type="text" class="input-item"/>
                                </div>
                                <div class="col-1">
                                    <label for="code">
                                        Code
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-1">
                                    <label for="title">
                                        title *
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                                <div class="col-1">
                                    <label for="address1">
                                        address1 *
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <label for="title">
                                        address2
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                                <div class="col-1">
                                    <label for="address1">
                                        address2
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <label for="contact_name">
                                        contact name
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                                <div class="col-1">
                                    <label for="contact_surname">
                                        contact surname
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <label for="contact_name">
                                        contact phone
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                                <div class="col-1">
                                    <label for="is_active">
                                        is active
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-1">
                                    <label for="name">
                                        name
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                                <div class="col-1">
                                    <label for="email">
                                        email
                                    </label>
                                    <input type="text" class="input-item"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <label for="password">
                                        password
                                    </label>
                                    <input type="password" class="input-item"/>
                                </div>
                                <div class="col-1">
                                    <label for="confirm_password">
                                        confirm password
                                    </label>
                                    <input type="password" class="input-item"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <label for="trials_end_at">
                                        trials end at
                                    </label>
                                    <input type="date" class="input-item"/>
                                </div>
                                <div class="col-1">
                                    <label for="module_name">
                                        module name
                                    </label>
                                    <select name="" id="" multiple>
                                        <option value="">sale</option>
                                        <option value="">purchase</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <button type="submit" class="info m">submit</button>
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
        name: "vendorCustomerCreate",
        mounted(){

        }
    }
</script>

<style scoped>

</style>