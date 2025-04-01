<template>
  <v-card outlined>
    <v-dialog
        width="800"
        v-model="exportDialog"
    >
      <expense-payment-export
          @closeDialogExport="closeDialogExport"
      >
      </expense-payment-export>
    </v-dialog>
    <v-dialog
        width="800"
        v-model="payment_bill_dialog"
    >
      <payment-bill-receipt
          v-if="payment_bill_dialog"
          :payment_id="payment_bill_id"
      ></payment-bill-receipt>
    </v-dialog>
    <v-card-title>
      List of Bill Paid
      <v-spacer></v-spacer>
      <v-tooltip
          bottom
      >
<!--          v-if="$options.filters.checkPermission(['expense', 'export'])"-->

        <template v-slot:activator="{ on, attrs }">
          <v-btn
              icon
              v-on="on"
              v-bind="attrs"
              @click="exportDialog=true"
          >
            <v-icon>
              mdi-database-export
            </v-icon>
          </v-btn>
        </template>
        <span>export</span>
      </v-tooltip>
      <v-tooltip
          bottom
      >
<!--          v-if="$options.filters.checkPermission(['expense', 'import'])"-->

        <template v-slot:activator="{ on, attrs }">
          <v-btn
              icon
              v-on="on"
              v-bind="attrs"
          >
            <v-icon
                @click="$refs.inputUpload.click()"
            >
              mdi-database-import
            </v-icon>
            <input
                type="file"
                v-show="false"
                id="inputUpload"
                ref="inputUpload"
                @change="uploadFile"
            >
          </v-btn>
        </template>
        <span>import</span>
      </v-tooltip>
      <v-btn icon @click="$htmlToPaper('print_div_list', {styles:['/css/app.css']})">
        <v-icon>mdi-printer</v-icon>
      </v-btn>
    </v-card-title>
    <v-card-text>
      <v-row>
        <v-col md="3" cols="12">
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
                  solo
                  readonly
                  clearable
                  v-on="on"
                  v-bind="attrs"
                  label="bill date"
                  v-model="options.date"
              >
              </v-text-field>
            </template>
            <v-date-picker
                range
                v-model="options.date"
                @input="options.date.length>1?menu = false:null"
            >
            </v-date-picker>
          </v-menu>
        </v-col>
        <v-col md="3" cols="12">
          <v-text-field
              solo
              clearable
              label="bill no"
              v-model="options.ref"
          ></v-text-field>
        </v-col>
        <v-col md="3" cols="12">
          <v-text-field
              solo
              clearable
              label="user by name"
              v-model="options.user_name"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="12" id="print_div_list">
          <v-data-table
              :headers="headers"
              :loading="loading"
              :items="items.data"
              class="elevation-0"
              :options.sync="options"
              :server-items-length="items.total"
              loading-text="Loading... Please wait"
              :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
          >
            <template v-slot:[`item.date`]="{item}">
              {{ item.date | removeTimeFromDate }}
            </template>
            <template v-slot:[`item.paid`]="{item}">
              <router-link :to="{name:'bank.transaction', params: {id:item.transaction_id}}">
                {{ item.paid }}
              </router-link>
            </template>
            <template v-slot:[`item.supplier_name`]="{item}">
              <span v-if="item.supplier_name">{{ item.supplier_name }}-supplier</span>
              <span v-if="item.customer_name">{{ item.customer_name }}-customer</span>
              <span v-if="item.employee_name">{{ item.employee_name }}-employee</span>
              <span v-if="item.otheruser_name">{{ item.otheruser_name }}-otheruser</span>
            </template>
            <template v-slot:[`item.action`]="{item}">
              <tooltip-button
                  text
                  icon-name="mdi-eye"
                  data-text="View"
                  size="x-small"
                  color="primary"
                  @click="payment_bill_dialog=true, payment_bill_id=item.id"
              />
            </template>
          </v-data-table>
        </v-col>
      </v-row>


    </v-card-text>
  </v-card>
</template>

<script src="./expense-bill-paid.js"></script>