<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Product Report
        <v-spacer></v-spacer>
        <v-btn
            icon
            @click="$htmlToPaper('list_print_bootstrap')">
          <v-icon>mdi-printer</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col>
            <v-text-field
                dense
                clearable
                label="name"
                hide-details
                v-model="options.name"
            />
          </v-col>
          <v-col>
            <v-text-field
                dense
                clearable
                label="code"
                hide-details
                v-model="options.code"
            />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="12" id="list_print_div">
            <v-data-table
                dense
                :headers="headers"
                :loading="loading"
                :items="items.data"
                class="elevation-0"
                :options.sync="options"
                :server-items-length="items.total"
                loading-text="Loading... Please wait"
                :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
            >
              <template v-slot:[`item.weight`]="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(warehouse, index) in item.warehouses" :key="index">
                      <td>{{ warehouse.name }} --- {{ warehouse.pivot.weight }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
              <template v-slot:item.stock_count="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(warehouse, index) in item.warehouses" :key="index">
                      <td>{{ warehouse.name }} --- {{ warehouse.quantityStr }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-card-text>
      <list-print-bootstrap
          :title="'Product Report'"
          :columns="headers"
          :rows="items.data"
          style="visibility: collapse"
          :date_fields="[]"
      />
      <v-dialog v-model="product_location_dialog" width="900">
        <a-product-locations-list
            v-if="product_location_dialog"
            :product-id="location_product_id"
        />
      </v-dialog>
    </v-card>
  </v-container>
</template>
<script src="./productReport.js"></script>
<!--<style scoped>-->
<!--::v-deep th {-->
<!--  padding: 0 3px !important;-->
<!--}-->
<!--</style>-->