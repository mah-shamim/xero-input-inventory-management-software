<template v-if="isLoaded">
    <div>
        <div class="sideMenu" id="sideBar">
            <div class="accordion no-shadow no-hover">
                <div>
                   <div v-if="feature_role.F_INV_13">
                            <button class="target dropcollaps">
                                <Icon type="cube" class="barIcon"></Icon>
                                Warehouse
                            </button>
                        </div>
                    <div class="panel" v-if="feature_role.F_INV_13">
                        <ul>
                            <li>
                                <router-link :to="{name:'warehousesCreate'}">
                                    <Icon type="plus-circled" class="barIcon"></Icon>
                                    Add Warehouse
                                </router-link>
                            </li>
                            <li>
                                <router-link
                                        :to="{name:'warehouseIndex'}">
                                    <Icon
                                            type="android-list" class="barIcon"></Icon>
                                    Warehouse list
                                </router-link>
                            </li>

                        </ul>
                    </div>
                </div>
                <div v-if="feature_role.F_INV_7">
                    <button class="target dropcollaps">
                        <Icon type="ios-person" class="barIcon"></Icon>
                        Supplier
                    </button>
                    <div class="panel">
                        <ul>
                            <li>
                                <router-link
                                        :to="{name:'suppliersCreate'}">
                                    <Icon
                                            type="person-add" class="barIcon"></Icon>
                                    Add Supplier
                                </router-link>
                            </li>
                            <li>
                                <router-link
                                        :to="{name:'supplierIndex'}">
                                    <Icon type="ios-list-outline" class="barIcon"></Icon>
                                    Supplier list
                                </router-link>
                            </li>

                        </ul>
                    </div>
                </div>
                <div v-if="feature_role.F_INV_11">
                    <button class="target dropcollaps">
                        <Icon type="ios-people" class="barIcon"></Icon>
                        Customer
                    </button>
                    <div class="panel">
                        <ul>
                            <li>
                                <router-link :to="{name:'customerCreate'}">
                                    <Icon type="person-add" class="barIcon"></Icon>
                                    Add Customer
                                </router-link>
                            </li>
                            <li>
                                <router-link
                                        :to="{name:'customerIndex'}">
                                    <Icon type="ios-list-outline" class="barIcon"></Icon>
                                    Customer list
                                </router-link>
                            </li>


                        </ul>
                    </div>
                </div>
                <div v-if="feature_role.F_INV_33">
                    <button class="target dropcollaps">
                        <Icon type="grid" class="barIcon"></Icon>
                        Category
                    </button>
                    <div class="panel">
                        <ul>
                            <li>
                                <router-link :to="{name:'categoriesCreate'}">
                                    <Icon type="android-exit" class="barIcon"></Icon>
                                    Add Category
                                </router-link>
                            </li>
                            <li>
                                <router-link
                                        :to="{name:'categoryIndex'}">
                                    <Icon type="social-buffer" class="barIcon"></Icon>
                                    Category list
                                </router-link>
                            </li>

                        </ul>
                    </div>
                </div>
                <div v-if="feature_role.F_INV_3">
                    <button class="target dropcollaps">
                        <Icon type="ribbon-b" class="barIcon"></Icon>
                        Brand
                    </button>
                    <div class="panel">
                        <ul>
                            <li>
                                <router-link :to="{name:'brandsCreate'}">
                                    <Icon type="android-add" class="barIcon"></Icon>
                                    Add Brand
                                </router-link>
                            </li>
                            <li>
                                <router-link
                                        :to="{name:'brandsIndex'}">
                                    <Icon type="grid" class="barIcon"></Icon>
                                    Brand list
                                </router-link>
                            </li>

                        </ul>
                    </div>
                </div>
                <div v-if="feature_role.F_INV_25">
                    <button class="target dropcollaps">
                        <Icon type="coffee" class="barIcon"></Icon>
                        Product
                    </button>
                    <div class="panel">
                        <ul>
                            <li>
                                <router-link :to="{name:'productsCreate'}">
                                    <Icon type="compose" class="barIcon"></Icon>
                                    Product Create
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'productsIndex'}">
                                    <Icon type="android-list" class="barIcon"></Icon>
                                    Product list
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'purchasesCreate'}">
                                    <Icon type="ios-pricetags" class="barIcon"></Icon>
                                    Product Purchase
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'purchaseIndex'}">
                                    <Icon type="ios-list-outline" class="barIcon"></Icon>
                                    Purchase List
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'salesCreate'}">
                                    <Icon type="android-cart" class="barIcon"></Icon>
                                    Product Sale
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'salesIndex'}">
                                    <Icon type="ios-paper" class="barIcon"></Icon>
                                    Sale List
                                </router-link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div v-if="feature_role.F_INV_45">
                    <button class="target dropcollaps">
                        <Icon type="settings" class="barIcon"></Icon>
                        Unit
                    </button>
                    <div class="panel">
                        <ul>
                            <li>
                                <router-link :to="{name:'unitCreate'}">
                                    <Icon type="android-add-circle" class="barIcon"></Icon>
                                    Unit Create
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'unitList'}">
                                    <Icon type="android-list" class="barIcon"></Icon>
                                    Unit List
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'unitMapping'}">
                                    <Icon type="map" class="barIcon"></Icon>
                                    Unit Mapping
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'productUnitCreate'}">
                                    <Icon type="map" class="barIcon"></Icon>
                                    Product Unit
                                </router-link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div v-if="feature_role.F_INV_29">
                    <button class="target dropcollaps">
                        <Icon type="cash" class="barIcon"></Icon>
                        Expense
                    </button>
                    <div class="panel">
                        <ul>
                            <li>
                                <router-link :to="{name:'createExpense'}">
                                    <Icon type="android-add-circle" class="barIcon"></Icon>
                                    Add Expense
                                </router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'listExpense'}">
                                    <Icon type="android-list" class="barIcon"></Icon>
                                    Expense List
                                </router-link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div v-if="feature_role.F_INV_29">
                    <router-link :to="{name:'settingsIndex'}" tag="button" class="dropcollaps">
                        <Icon type="battery-charging" class="barIcon"></Icon>
                        Settings
                    </router-link>
                </div>
                <button class="target dropcollaps">
                    <Icon type="pie-graph" class="barIcon" style="font-size: 19px"></Icon>
                    Report/Ledger
                </button>
                <div class="panel">
                    <ul>
                        <div v-if="feature_role.F_INV_57">
                            <li>
                                <router-link :to="{name:'purchaseReportIndex'}">
                                    <Icon type="arrow-return-left" class="barIcon"></Icon>
                                    Purchase
                                </router-link>
                            </li>
                        </div>
                        <div v-if="feature_role.F_INV_53">
                            <li>
                                <router-link :to="{name:'actualPurchaseReportIndex'}">
                                    <Icon type="arrow-return-left" class="barIcon"></Icon>
                                    Actual Purchase
                                </router-link>
                            </li>
                        </div>
                        <div v-if="feature_role.F_INV_58">
                            <li>
                                <router-link :to="{name:'saleReportIndex'}">
                                    <Icon type="arrow-return-right" class="barIcon"></Icon>
                                    Sale
                                </router-link>
                            </li>
                        </div>
                        <div v-if="feature_role.F_INV_59">
                            <li>
                                <router-link :to="{name:'productReportIndex'}">
                                    <Icon type="coffee" class="barIcon"></Icon>
                                    Product
                                </router-link>
                            </li>
                        </div>
                        <div v-if="feature_role.F_INV_29">
                            <li>
                                <router-link :to="{name:'warehouseReportIndex'}">
                                    <Icon type="social-buffer" class="barIcon"></Icon>
                                    Warehouse
                                </router-link>
                            </li>
                        </div>
                        <div v-if="feature_role.F_INV_54">
                            <li>
                                <router-link :to="{name:'expenseReportIndex'}">
                                    <Icon type="cash" class="barIcon"></Icon>
                                    Expense
                                </router-link>
                            </li>
                        </div>
                        <div v-if="feature_role.F_INV_55">
                            <li>
                                <router-link :to="{name:'overallReportIndex'}">
                                    <Icon type="cash" class="barIcon"></Icon>
                                    Overall
                                </router-link>
                            </li>
                        </div>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                feature_role:{},
                isLoaded:false
            }
        },

        created(){
            this.isLoaded=true

        }
    }
</script>

<style>

</style>
