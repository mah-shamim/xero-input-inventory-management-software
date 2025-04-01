<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Warehouse Report
        <v-spacer></v-spacer>
        <v-btn icon @click="$htmlToPaper('list_print_bootstrap')">
          <v-icon>mdi-printer</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-row justify="end">
          <v-col cols="12" md="4">
            <v-text-field
                dense
                clearable
                @click.stop
                hide-details
                label="product code"
                v-model="options.code"
            />
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field
                dense
                clearable
                @click.stop
                hide-details
                label="product name"
                v-model="options.name"
            />
          </v-col>
          <v-col cols="12" md="4">
            <v-autocomplete
                dense
                item-value="id"
                item-text="name"
                :items="warehouseList"
                lebel="select warehouse"
                v-model="options.warehouse_id"
                prepend-icon="mdi-database-search"
            >
            </v-autocomplete>
          </v-col>
        </v-row>
        <v-row>
          <v-col md="12" cols="12" id="list_print_div">
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
              <template v-slot:header.name="{ header }">

              </template>
              <template v-slot:header.code="{ header }">

              </template>
              <template v-slot:[`item.part_number`]="{item}">
                <v-btn
                    dark
                    icon
                    x-small
                    color="primary"
                    @click="showProductLocationDialog=true, product_id=item.id"
                >
                  <v-icon>mdi-package-variant</v-icon>
                </v-btn>
                <v-dialog
                    width="1000"
                    v-model="part_number_dialog"
                >
                  <v-card>
                    <v-card-title>
                      Part Number Detail
                    </v-card-title>
                    <v-card-text>
                      <v-data-table
                          dense
                          class="elevation-0"
                          :headers="part_number_headers"
                          :items="part_number_data.data"
                          :options.sync="part_number_options"
                          :server-items-length="part_number_data.total"
                          :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
                      >
                        <template v-slot:top>
                          <v-row>
                            <v-col md="4" cols="12">
                              <v-text-field
                                  clearable
                                  label="search by part number"
                                  v-model="part_number_options.part_number"
                              ></v-text-field>
                            </v-col>
                          </v-row>
                          <br>
                        </template>
                        <template v-slot:[`item.sales`]="{item}">
                          <router-link
                              v-if="!_.isEmpty(item.sale)"
                              :to="{name:'sale.show', params:{id:item.sale.id}}"
                          >
                            {{ item.sale.ref }}
                          </router-link>
                        </template>
                        <template v-slot:[`item.purchase`]="{item}">
                          <router-link
                              :to="{name:'purchase.show', params:{id:item.purchase.id}}"
                          >
                            {{ item.purchase.bill_no }}
                          </router-link>
                        </template>
                      </v-data-table>
                    </v-card-text>
                  </v-card>
                </v-dialog>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-card-text>
      <v-dialog v-model="showProductLocationDialog" width="1000">
        <product-location-dialog
            :warehouse-id="options.warehouse_id"
            v-if="showProductLocationDialog"
            :product-id="product_id"
        />
      </v-dialog>
      <list-print-bootstrap
          :title="`Warehouse Report ${options.warehouse_id?_.find(warehouseList,{id:options.warehouse_id}).name:''}`"
          :columns="headers"
          :rows="items.data"
          style="visibility: collapse"
          :date_fields="[]"
          :remove_columns="['part_number']"
      />
    </v-card>
  </v-container>
</template>

<script src="./warehouseReport.js"></script>