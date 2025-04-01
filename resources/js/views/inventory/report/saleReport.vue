<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Sale Report
        <v-spacer></v-spacer>
        <v-btn icon @click="$htmlToPaper('list_print_bootstrap')">
          <v-icon>mdi-printer</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col>
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
                    prepend-icon="event"
                    v-model="options.sales_date"
                >
                </v-text-field>
              </template>
              <v-date-picker
                  range
                  v-model="options.sales_date"
                  @input="options.sales_date.length>1?menu = false:null"
              >
              </v-date-picker>
            </v-menu>
          </v-col>
          <v-col>
            <v-text-field
                clearable
                label="ref"
                v-model="options.ref"
                prepend-icon="mdi-database-search"
            >
            </v-text-field>
          </v-col>
          <v-col>
            <v-text-field
                clearable
                label="customer name"
                v-model="options.customer"
                prepend-icon="mdi-database-search"
            >
            </v-text-field>
          </v-col>
          <v-col>
            <v-text-field
                clearable
                label="product name"
                v-model="options.product"
                prepend-icon="mdi-database-search"
            >
            </v-text-field>
          </v-col>
          <v-col>
            <v-text-field
                clearable
                label="salesman code"
                v-model="options.salesman_code"
                prepend-icon="mdi-database-search"
            >
            </v-text-field>
          </v-col>
        </v-row>
        <v-row>
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
              <template v-slot:item.ref="{ item }">
                {{ item.ref }}
              </template>
              <template v-slot:item.products="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(product, index) in item.products" :key="'pn'+index">
                      <td>{{ index + 1 }}.{{ product.name }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
              <template v-slot:item.trans_type="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(payment, index) in item.payments" :key="'pp'+index">
                      <td>{{ $root.paymentMethods(payment.payment_type) }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
              <template v-slot:item.quantity="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(product, index) in item.products" :key="'pq'+index">
                      <td>{{ index + 1 }}. {{ product.quantityStr }}</td>
                    </tr>
                    </tbody>
                  </template>
                </v-simple-table>
              </template>
              <template v-slot:item.price="{ item }">
                <v-simple-table dense>
                  <template v-slot:default>
                    <tbody>
                    <tr v-for="(product, index) in item.products" :key="'ppp'+index">
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
                    <tr v-for="(payment, index) in item.payments" :key="'pda'+index">
                      <td>{{ parseFloat(payment.paid) }}</td>
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
                  <th class="text-right">Total</th>
                  <th>{{ totalPurchase[0] }}</th>
                  <th>{{ totalPurchase[1] }}</th>
                  <th>{{ totalPurchase[2] }}</th>
                </tr>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-card-text>
      <sale-report-print
          :title="'Sale Report'"
          :totalPurchase="totalPurchase"
          :columns="headers"
          :rows="items.data"
          style="visibility: collapse"
          :date-fields="['purchase_date_formatted']"

      />
    </v-card>
  </v-container>
</template>
<script src="./saleReport.js"></script>