<template>
    <div class="centralize">
        <div class="row" id="order-list">
            <Tabs @on-click="clickTab">
                <TabPane label="New" icon="android-cart">
                    <div class="col-1">
                        <h5 class="formTitle">
                            New Purchase Order List -
                            <Icon type="ios-list-outline"></Icon>
                        </h5>
                        <div class="row">
                            <div class="col-1">
                                <input type="text" class="input-item" placeholder="Search by Order No.."
                                       v-model="orderNo"/>
                            </div>
                            <div class="col-1">
                                <input type="text" class="input-item" placeholder="Search by name.."
                                       v-model="supplierName"/>
                            </div>
                            <div class="col-1">
                                <input type="text" class="input-item" placeholder="Search by biller.."
                                       v-model="biller"/>
                            </div>
                        </div>
                        <!--TODO: check deep link in a recursive loop-->
                        <inventory-lists :model="'orders/purchase'"
                                         :module="'inventory'"
                                         :showPayment=true
                                         :queryString="queryString + '&status=New'"
                                         :showHide=true
                                         :toolbar="true"
                                         @process-order="processOrder"
                                         @cancel-order="cancelOrder"
                                         :toolbarAction="[
                                             {name:'Process', action:'process-order'},
                                             {name:'Cancel', action:'cancel-order'}
                                         ]"
                                         :selectable=true
                                         :gridColumns="[
                                     {field: 'order_no', column: 'Order No.'},
                                     {field: 'status', column: 'Status'},
                                     {field: 'supplier_name', column: 'Supplier Name'},
                                     {field: 'biller', column: 'Biller Name'},
                                     {field: 'order_date', column: 'Order Date'},
                                     {field: 'created_at', column: 'Created Date'}
                                 ]"
                        >
                        </inventory-lists>
                    </div>
                </TabPane>
                <TabPane label="Processing" icon="gear-a">
                    <div class="col-1">
                        <h5 class="formTitle">
                            Processing Purchase Order List -
                            <Icon type="ios-list-outline"></Icon>
                        </h5>
                        <div class="row">
                            <div class="col-1">
                                <input type="text" class="input-item" placeholder="Search by Order No.."
                                       v-model="orderNo"/>
                            </div>
                            <div class="col-1">
                                <input type="text" class="input-item" placeholder="Search by name.."
                                       v-model="supplierName"/>
                            </div>
                            <div class="col-1">
                                <input type="text" class="input-item" placeholder="Search by biller.."
                                       v-model="biller"/>
                            </div>


                        </div>
                        <!--TODO: check deep link in a recursive loop-->
                        <inventory-lists :model="'orders/purchase'"
                                         :module="'inventory'"
                                         :showPayment=true
                                         :queryString="queryString + '&status=Processing'"
                                         :showHide=true
                                         :toolbar="true"
                                         @confirm-order="confirmOrder"
                                         @cancel-order="cancelOrder"
                                         :toolbarAction="[
                                             {name:'Confirm', action:'confirm-order'},
                                             {name:'Cancel', action:'cancel-order'}
                                         ]"
                                         :selectable=true
                                         :gridColumns="[
                                     {field: 'order_no', column: 'Order No.'},
                                     {field: 'status', column: 'Status'},
                                     {field: 'supplier_name', column: 'Supplier Name'},
                                     {field: 'biller', column: 'Biller Name'},
                                     {field: 'order_date', column: 'Order Date'},
                                     {field: 'created_at', column: 'Created Date'}
                                 ]"
                        >
                        </inventory-lists>
                    </div>
                </TabPane>
                <TabPane label="Completed" icon="checkmark-circled">
                    <div class="col-1">
                        <h5 class="formTitle">
                            Completed Purchase Order List -
                            <Icon type="ios-list-outline"></Icon>
                        </h5>
                        <div class="row">
                            <div class="col-1">
                                <input type="text" class="input-item" placeholder="Search by Order No.."
                                       v-model="orderNo"/>
                            </div>
                            <div class="col-1">
                                <input type="text" class="input-item" placeholder="Search by name.."
                                       v-model="supplierName"/>
                            </div>
                            <div class="col-1">
                                <input type="text" class="input-item" placeholder="Search by biller.."
                                       v-model="biller"/>
                            </div>


                        </div>
                        <!--TODO: check deep link in a recursive loop-->
                        <inventory-lists :model="'orders/purchase'"
                                         :module="'inventory'"
                                         :showPayment=true
                                         :queryString="queryString + '&status=Completed'"
                                         :showHide=true
                                         :gridColumns="[
                                     {field: 'order_no', column: 'Order No.'},
                                     {field: 'status', column: 'Status'},
                                     {field: 'supplier_name', column: 'Supplier Name'},
                                     {field: 'biller', column: 'Biller Name'},
                                     {field: 'order_date', column: 'Order Date'},
                                     {field: 'created_at', column: 'Created Date'}
                                 ]"
                        >
                        </inventory-lists>
                    </div>
                </TabPane>
            </Tabs>

        </div>

    </div>
</template>

<script src="./js/index.js"></script>