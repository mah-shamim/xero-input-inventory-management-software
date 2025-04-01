<template>
    <div class="centralize">
        <div class="row">
            <div class="col-1">
                <h5 class="formTitle">
                    Product Tier Price List -
                    <Icon type="ios-paper-outline"></Icon>
                </h5>
                <div class="row">
                    <div class="col-1">
                        <input type="text" class="input-item" v-model="name"
                               placeholder="Search by Product Name..."/>
                    </div>
                    <div class="col-1">
                        <input type="text" class="input-item" v-model="code"
                               placeholder="Search by Product Code..."/>
                    </div>

                </div>
                <inventory-lists :model="'product-tires-prices'"
                                 :module="'inventory'"
                                 :sortOrder=true
                                 :sortKey="'id'"
                                 :gridColumns="[
                                     {field: 'created_at', column: 'Created Date'}
                                 ]"
                >
                </inventory-lists>
            </div>
        </div>
    </div>

</template>

<script src="./js/index.js"></script>

<style>

</style>