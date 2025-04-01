<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Purchase Report
        <v-spacer/>
        <v-btn
            icon
            @click="$htmlToPaper('list_print_bootstrap')"
        >
          <v-icon>mdi-printer</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col md="2" cols="12">
            <v-menu
                offset-y
                v-model="menu"
                min-width="290px"
                :nudge-right="40"
                transition="scale-transition"
                :close-on-content-click="false"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    readonly
                    v-on="on"
                    v-bind="attrs"
                    label="date range"
                    v-model="options.purchase_date"
                />
              </template>
              <v-date-picker
                  range
                  v-model="options.purchase_date"
                  @input="options.purchase_date.length>1?menu = false:null"
              />
            </v-menu>
          </v-col>
          <v-col md="2" cols="12">
            <v-select
                clearable
                item-value="id"
                item-text="name"
                :items="warehouses"
                label="search warehouse"
                v-model="options.warehouse_id"
            />
          </v-col>
          <v-col md="2" cols="12">
            <v-text-field
                clearable
                label="bill no"
                v-model="options.bill_no"
            />
          </v-col>
          <v-col md="2" cols="12">
            <v-text-field
                clearable
                label="company"
                v-model="options.company"
            />
          </v-col>
          <v-col md="2" cols="12">
            <v-text-field
                clearable
                label="products"
                v-model="options.product"
            />
          </v-col>
          <v-col md="2" cols="12">
            <v-text-field
                clearable
                label="part number"
                v-model="options.part_number"
            />
          </v-col>

          <v-col md="12" cols="12" id="print_div_list">
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
              <template v-slot:item.products="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(product, index) in item.products" :key="'n'+index">
                      <td>{{ index + 1 }}.{{ product.name }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
              <template v-slot:item.quantity="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(product, index) in item.products" :key="'ps'+index">
                      <td>{{ index + 1 }}. {{ product.quantityStr }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
              <template v-slot:item.warehouse="{ item }">
                <template v-if="showColumn">
                <span v-for="(product, index) in $options.filters.uniqueByKeyValue(item.products, 'warehouse_name')">
                  {{ product.warehouse_name }},
                </span>
                </template>
                <v-simple-table dense v-else>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(product, index) in item.products">
                      <td>{{ product.warehouse_name }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
              <template v-slot:item.base_quantity="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr
                        v-for="(product, index) in item.products"
                        :key="'pu'+index"
                    >
                      <td>
                        {{ index + 1 }}. {{ parseFloat(product.quantityBaseUnit).toFixed(2) }}
                        ({{ product.unit.key }})
                      </td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
              <template v-slot:item.price="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(product, index) in item.products" :key="'pp'+index">
                      <td>{{ parseFloat(product.pivot.price) }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
              <template v-slot:item.discount="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(product, index) in item.products" :key="'pd'+index">
                      <td>{{ product.pivot.discount }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
              <template v-slot:item.credit="{item}">
                <v-simple-table dense v-if="!_.includes(singleLines, 'credit')">
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(payment, index) in item.payments" :key="index">
                      <td> {{ parseFloat(payment.paid) }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
                <span v-else>
                  {{ _.sumBy(item.payments, 'paid') }}
              </span>
              </template>
              <template v-slot:item.balance="{item}">
                {{ $root.$data.erp.report.checkPaymentStatus(item.total, item.payments) }}
              </template>
              <template slot="body.append">
                <tr v-if="totalPurchase && totalPurchase.length>0">
                  <th :colspan="9 - columns.length"></th>
                  <th>Total</th>
                  <th>{{ totalPurchase[0] }}</th>
                  <th>{{ totalPurchase[1] }}</th>
                  <th>{{ totalPurchase[2] }}</th>
                </tr>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-card-text>
      <purchase-report-print
          :title="'Purchase Report'"
          :totalPurchase="totalPurchase"
          :columns="headers"
          :rows="items.data"
          style="visibility: collapse"
          :date-fields="['purchase_date_formatted']"

      />
    </v-card>
  </v-container>
</template>

<script src="./purchaseReport.js"></script>