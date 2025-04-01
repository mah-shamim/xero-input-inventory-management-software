<template>
  <v-container fluid>
    <v-card flat>
      <!--    <v-dialog width="800" v-model="export_dialog">-->
      <!--      <expense-export-->
      <!--          @closeDialogExport="closeDialogExport"-->
      <!--      >-->
      <!--      </expense-export>-->
      <!--    </v-dialog>-->
      <v-dialog
          width="900"
          v-model="payment_bill_dialog"
      >
        <payment-bill-receipt
            :payment_id="payment_bill_id"
            v-if="payment_bill_dialog"
        ></payment-bill-receipt>
      </v-dialog>
      <v-dialog
          width="1400"
          v-model="expenseBillPaidDialog"
      >
        <expense-bill-paid
            v-if="expenseBillPaidDialog"
        >
        </expense-bill-paid>
      </v-dialog>
      <v-dialog
          scrollable
          width="800"
          v-model="payment_crud_dialog"
      >
        <payment-crud
            model="expense"
            :default_payment_type="3"
            v-if="payment_crud_dialog"
            :method="payment_crud_method"
            @paymentSuccess="paymentSuccess"
            :model_id="payment_crud_model_id"
            :payment_id="payment_crud_payment_id"
            v-model="payment_crud_dialog"
        >
        </payment-crud>
      </v-dialog>
      <v-dialog width="800" v-model="payment_list_dialog">
        <v-card outlined class="elevation-0">
          <v-card-title>Bills</v-card-title>
          <v-card-text>
            <v-data-table
                :items="payments"
                class="elevation-0"
                hide-default-footer
                :headers="paymentHeaders"
                loading-text="Loading... Please wait"
            >
              <template v-slot:[`item.expense_date`]="{item}">
                {{ item.expense_date | removeTimeFromDate }}
              </template>
              <!--            <template v-slot:[`item.paid`]="{item}">-->
              <!--              <router-link-->
              <!--                  :to="{name:'bank.transaction', params:{id:item.transaction_id}}"-->
              <!--              >-->
              <!--                {{ item.paid | toFix(2) }}-->
              <!--              </router-link>-->
              <!--            </template>-->

              <!--                  v-if="$options.filters.checkPermission(['purchase', 'edit'])"-->
              <template v-slot:[`item.action`]="{item}">
                <v-btn
                    text
                    icon
                    x-small
                    color="success"
                    @click="editBill(item.id)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                    text
                    icon
                    x-small
                    color="primary"
                    @click="payment_bill_dialog=true, payment_bill_id=item.id"
                >
                  <v-icon>mdi-eye</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-dialog>
      <v-dialog scrollable max-width="1400" v-model="expenseDialog">
        <expense-create
            :id="form_id"
            v-if="expenseDialog"
            @fromCreate="fromCreate"
            v-model="expenseDialog"
        >
        </expense-create>
      </v-dialog>
      <v-dialog max-width="1000" v-model="expenseShowDialog">
        <expense-show
            :form_id="form_id"
            v-if="form_id && expenseShowDialog"
        >
        </expense-show>
      </v-dialog>
      <v-card-title>
        Expenses
        <v-spacer/>
        <!--          v-if="$options.filters.checkPermission(['expense', 'create'])"-->
        <v-btn
            icon
            dark
            dusk="create"
            color="primary"
            @click="expenseDialog=true"
        >
          <v-icon>{{ $root.icons.create }}</v-icon>
        </v-btn>
        <v-btn
            dark
            x-small
            color="success"
            @click="expenseBillPaidDialog=true"
        >
          payment list
        </v-btn>

        <!--          v-if="$options.filters.checkPermission(['expense', 'export'])"-->
        <v-tooltip
            bottom
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                v-bind="attrs"
                v-on="on"
                icon
                @click="export_dialog=true"
            >
              <v-icon>mdi-database-export</v-icon>
            </v-btn>
          </template>
          <span>export</span>
        </v-tooltip>
        <!--      v-if="$options.filters.checkPermission(['expense', 'import'])"-->
        <v-tooltip
            bottom
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn v-bind="attrs" v-on="on" icon>
              <v-icon @click="$refs.inputUpload.click()">mdi-database-import</v-icon>
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
        <v-btn icon @click="$htmlToPaper('list_print_bootstrap')">
          <v-icon>mdi-printer</v-icon>
        </v-btn>
        <v-btn
            icon
            @click="resetQuery()"
        >
          <v-icon>mdi-autorenew</v-icon>
        </v-btn>
      </v-card-title>
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
                  dense
                  readonly
                  clearable
                  v-on="on"
                  @click.stop
                  hide-details
                  v-bind="attrs"
                  label="expense date"
                  v-model="options.expense_date"
              >
              </v-text-field>
            </template>
            <v-date-picker
                range
                v-model="options.expense_date"
                @input="options.expense_date.length>1?menu = false:null"
            >
            </v-date-picker>
          </v-menu>
        </v-col>
        <v-col>
          <v-select
              dense
              clearable
              hide-details
              item-value="id"
              item-text="name"
              :items="accounts"
              label="select account"
              v-model="options.account_id"
          />
        </v-col>
        <v-col>
          <v-text-field
              dense
              clearable
              @click.stop
              hide-details
              label="amount"
              v-model="options.amount"
          />
        </v-col>
        <v-col>
          <v-text-field
              dense
              clearable
              hide-details
              label="warehouse"
              v-model="options.warehouse"
          />
        </v-col>
        <v-col>
          <v-text-field
              dense
              clearable
              @click.stop
              hide-details
              label="bill no"
              v-model="options.ref"
          />
        </v-col>
      </v-row>
      <v-card-text>
        <v-row>
          <v-col md="12" cols="12" id="print_div">
            <v-data-table
                :headers="headers"
                :loading="loading"
                :items="items.data"
                class="elevation-0 expense-table"
                :options.sync="options"
                :server-items-length="items.total"
                loading-text="Loading... Please wait"
                :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
            >
              <template v-slot:[`item.paid_total`]="{item}">
                {{ item.paid_total | toFix(2) }}
              </template>
              <template v-slot:[`item.due`]="{item}">
                {{ item.due | toFix(2) }}
              </template>
              <template v-slot:[`item.expense_date`]="{item}">
                {{ item.expense_date | removeTimeFromDate }}
              </template>
              <template v-slot:[`item.amount`]="{item}">
                {{ item.amount | toFix(2) }}
              </template>
              <!--            v-if="$options.filters.checkPermission(['expense', 'edit']) || $options.filters.checkPermission(['expense', 'delete'])"-->

              <template
                  v-slot:item.action="{ item }"
              >
                <div class="text-center">
                  <v-menu offset-y>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                          dark
                          x-small
                          v-on="on"
                          color="primary"
                          v-bind="attrs"
                      >
                        actions
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item
                      >
                        <tooltip-button
                            icon
                            size="small"
                            color="success"
                            data-text="View"
                            icon-name="mdi-eye"
                            @click="showExpense(item.id)"
                        />
                      </v-list-item>
                      <v-list-item>
                        <tooltip-button
                            icon
                            size="small"
                            color="primary"
                            :icon-name="$root.icons.edit"
                            data-text="Edit"
                            @click="editExpense(item.id)"
                        />
                        <!--                          v-if="$options.filters.checkPermission(['expense', 'edit'])"-->

                      </v-list-item>
                      <v-list-item>
                        <tooltip-button
                            icon
                            color="red"
                            size="small"
                            :icon-name="$root.icons.delete"
                            data-text="Delete"
                            @click="deleteItem(item.id)"
                        />
                        <!--                          v-if="$options.filters.checkPermission(['expense', 'delete'])"-->

                      </v-list-item>
                      <v-list-item>
                        <tooltip-button
                            icon
                            size="small"
                            color="success"
                            data-text="Payment"
                            icon-name="mdi-cash"
                            @click="createExpenseBillById(item.id)"
                        />
                        <!--                          v-if="$options.filters.checkPermission(['purchase', 'create'])"-->

                      </v-list-item>
                      <v-list-item v-if="item.paid_total">
                        <tooltip-button
                            icon
                            size="small"
                            color="primary"
                            data-text="Bill List"
                            icon-name="mdi-playlist-check"
                            @click="getListOfExpenseBills(item.id)"
                        />
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-card-text>
      <list-print-bootstrap
          :title="'Expenses'"
          :columns="headers"
          :rows="items.data"
          style="visibility: collapse"
          :date_fields="['created_at', 'expense_date']"
      >
      </list-print-bootstrap>
    </v-card>
  </v-container>
</template>
<script src="./index.js"></script>
<style scoped>
::v-deep .expense-table th {
  padding: 0 3px !important;
}
</style>