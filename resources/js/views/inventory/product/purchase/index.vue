<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Purchases
        <v-spacer></v-spacer>
        <action-btn
            text="Create"
            dusk="create"
            :icon="$root.icons.create"
            @click="createOrUpdateDialog=true"
        />
        <collapse-btn @click="dense=!dense"/>
        <v-btn
            text
            small
            outlined
            class="mr-1"
            color="warning"
            @click="$htmlToPaper('list_print_bootstrap')"
        >
          Print
          <v-icon>{{ $root.icons.print }}</v-icon>
        </v-btn>
        <v-btn
            text
            small
            outlined
            class="mr-1"
            color="success"
            @click="payment_crud_dialog=true"
        >
          Payment
          <v-icon>{{ $root.icons.payment }}</v-icon>
        </v-btn>
        <v-btn
            text
            small
            outlined
            color="success"
            @click="isShowFilter=!isShowFilter"
        >
          {{ `${isShowFilter?'Hide Filter':'Show Filter'}` }}
          <v-icon>{{ `${isShowFilter?$root.icons.filter:$root.icons.filterOff}` }}</v-icon>
        </v-btn>
      </v-card-title>
      <!--      <v-dialog width="800" v-model="export_dialog">-->
      <!--        <purchase-export-->
      <!--            @closeDialogExport="closeDialogExport"-->
      <!--        >-->
      <!--        </purchase-export>-->
      <!--      </v-dialog>-->

      <!--      <v-dialog-->
      <!--          width="800"-->
      <!--          v-model="payment_crud_dialog"-->
      <!--      >-->
      <!--      </v-dialog>-->
      <!--      <v-card-title>-->
      <!--        Purchases-->
      <!--        <v-spacer/>-->
      <!--        <tooltip-button-->
      <!--            icon-->
      <!--            dark-->
      <!--            color="primary"-->
      <!--            position="bottom"-->
      <!--            data-text="Create Purchase"-->
      <!--            :icon-name="$root.icons.create"-->
      <!--            @click="createOrUpdateDialog=true"-->
      <!--        />-->
      <!--        <v-btn-->
      <!--            x-small-->
      <!--            color="success"-->
      <!--            @click="createBill()"-->
      <!--            v-if="(['purchase', 'create'])"-->
      <!--        >-->
      <!--          Pay Bill-->
      <!--        </v-btn>-->
      <!--        <v-tooltip-->
      <!--            bottom-->
      <!--            v-if="(['purchase', 'export'])"-->
      <!--        >-->
      <!--          <span>Export</span>-->
      <!--          <template v-slot:activator="{ on }">-->
      <!--            <v-btn icon v-on="on" @click="export_dialog=true">-->
      <!--              <v-icon>mdi-database-export</v-icon>-->
      <!--            </v-btn>-->
      <!--          </template>-->
      <!--        </v-tooltip>-->
      <!--        <v-tooltip-->
      <!--            bottom-->
      <!--            v-if="(['purchase', 'import'])"-->
      <!--        >-->
      <!--          <span>Import</span>-->
      <!--          <template v-slot:activator="{ on }">-->
      <!--            <v-btn-->
      <!--                icon-->
      <!--                v-on="on"-->
      <!--            >-->
      <!--              <v-icon-->
      <!--                  @click="$refs.inputUpload.click()"-->
      <!--              >-->
      <!--                mdi-database-import-->
      <!--              </v-icon>-->
      <!--              <input-->
      <!--                  type="file"-->
      <!--                  v-show="false"-->
      <!--                  id="inputUpload"-->
      <!--                  ref="inputUpload"-->
      <!--                  @change="uploadFile"-->
      <!--              >-->
      <!--            </v-btn>-->
      <!--          </template>-->
      <!--        </v-tooltip>-->
      <!--        <v-btn icon @click="$htmlToPaper('list_print_bootstrap')">-->
      <!--          <v-icon>{{ $root.appIcons.print }}</v-icon>-->
      <!--        </v-btn>-->
      <!--        <v-menu-->
      <!--            v-model="columnMenu"-->
      <!--            offset-overflow-->
      <!--            :close-on-content-click="false"-->
      <!--        >-->
      <!--          <template v-slot:activator="{ on, attrs }">-->
      <!--            <v-btn plain v-bind="attrs" v-on="on">-->
      <!--              <v-icon>mdi-cog</v-icon>-->
      <!--            </v-btn>-->
      <!--          </template>-->
      <!--          <v-card width="650">-->
      <!--            <v-card-title>Remove Column</v-card-title>-->
      <!--            <v-card-text>-->
      <!--              <v-row>-->
      <!--                <v-col-->
      <!--                    md="3"-->
      <!--                    cols="12"-->
      <!--                    class="py-0"-->
      <!--                    :key="'i' + i"-->
      <!--                    v-for="(h, i) in headers"-->
      <!--                >-->
      <!--                  <v-checkbox-->
      <!--                      class="my-0"-->
      <!--                      color="error"-->
      <!--                      :label="h.text"-->
      <!--                      :value="h.align"-->
      <!--                      :indeterminate="! !!h.align"-->
      <!--                      @click="!h.align?h.align='d-none':h.align=''"-->
      <!--                  />-->
      <!--                </v-col>-->
      <!--              </v-row>-->
      <!--            </v-card-text>-->
      <!--            <v-divider></v-divider>-->
      <!--            <v-card-actions>-->
      <!--              <v-spacer/>-->
      <!--              <v-btn-->
      <!--                  text-->
      <!--                  color="success"-->
      <!--                  @click="columnMenu = false"-->
      <!--              >-->
      <!--                Close-->
      <!--              </v-btn>-->
      <!--              <v-btn text color="primary" @click="headers.forEach(h=>h.align='')">Reset</v-btn>-->
      <!--            </v-card-actions>-->
      <!--          </v-card>-->
      <!--        </v-menu>-->
      <!--      </v-card-title>-->
      <v-card-text>
        <!--      <v-row justify="end">-->
        <!--        <v-col md="3" cols="12">-->
        <!--          <v-text-field-->
        <!--              solo-->
        <!--              label="search by part number"-->
        <!--              v-model="options.part_number"-->
        <!--          >-->
        <!--          </v-text-field>-->
        <!--        </v-col>-->
        <!--      </v-row>-->
        <v-row v-if="isShowFilter">
          <v-col>
            <v-text-field
                dense
                hide-details
                label="company or id"
                v-model="options.company"
            />
          </v-col>
          <v-col>
            <v-text-field
                dense
                @click.stop
                hide-details
                label="bill no"
                v-model="options.bill_no"
            />
          </v-col>
          <v-col>
            <v-menu
                v-model="menu"
                min-width="290px"
                :nudge-right="40"
                transition="scale-transition"
                :close-on-content-click="false"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    dense
                    clearable
                    readonly
                    v-on="on"
                    @click.stop
                    hide-details
                    v-bind="attrs"
                    label="date range"
                    v-model="options.purchase_date"
                >
                </v-text-field>
              </template>
              <v-date-picker
                  range
                  v-model="options.purchase_date"
                  @input="options.purchase_date.length>1?menu = false:null"
              >
              </v-date-picker>
            </v-menu>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="12">
            <v-data-table
                :dense="dense"
                :headers="headers"
                :loading="loading"
                :items="items.data"
                class="elevation-0 purchase-table"
                :options.sync="options"
                :server-items-length="items.total"
                loading-text="Loading... Please wait"
                :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
            >
              <template v-slot:[`item.bill_no`]="{item}">
                {{ item.bill_no }}
                <template v-if="item.returns_count">
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-icon
                          small
                          color="primary"
                          dark
                          v-bind="attrs"
                          v-on="on"
                      >
                        {{ $root.icons.hasReturn }}
                      </v-icon>
                    </template>
                    <span>Number of Product Returns: {{ item.returns_count }}</span>
                  </v-tooltip>
                </template>
              </template>
              <template v-slot:[`item.purchase_date`]="{item}">
                {{ item.purchase_date_formatted }}
              </template>
              <template v-slot:[`item.total_paid`]="{item}">
                {{ item.total_paid | toFix(2) }}
              </template>
              <template v-slot:[`item.due`]="{item}">
                {{ item.due | toFix(2) }}
              </template>

              <template v-slot:[`item.total`]="{item}">
                {{ item.total | toFix(2) }}
              </template>
              <template v-slot:[`item.total_weight`]="{item}">
                {{ item.total_weight | toFix(0) }}
                {{
                  $store.state.settings.settings.inventory !== undefined && item.total_weight > 0
                      ? $store.state.settings.settings.inventory.weight_unit
                      : ''
                }}
              </template>
              <template v-slot:item.action="{ item, index }">
                <v-menu dusk="action" top :close-on-content-click="closeOnContentClick">
                  <template v-slot:activator="{ on }">
                    <v-btn
                        dark
                        x-small
                        v-on="on"
                        dusk="action"
                        color="primary"
                    >
                      action
                    </v-btn>
                  </template>
                  <v-list>
                    <v-list-item>
                      <tooltip-button
                          :icon="true"
                          size="small"
                          color="primary"
                          data-text="View"
                          icon-name="mdi-eye"
                          @click="show_id=item.id"
                      />
                    </v-list-item>
                    <v-list-item>
                      <tooltip-button
                          :icon="true"
                          size="small"
                          color="primary"
                          data-text="Edit"
                          :dusk="`edit-${index}`"
                          :icon-name="$root.icons.edit"
                          @click="edit_id=item.id, createOrUpdateDialog=true"
                      />
                    </v-list-item>
                    <v-list-item v-if="item.due>0">
                      <tooltip-button
                          :icon="true"
                          size="small"
                          color="success"
                          data-text="Pay Bill"
                          :icon-name="$root.icons.payment"
                          :dusk="`payment-${index}`"
                          @click="createBillById(item.id)"
                      />
                    </v-list-item>
                    <v-list-item v-if="item.total_paid>0">
                      <tooltip-button
                          :icon="true"
                          color="primary"
                          data-text="Bills"
                          :icon-name="$root.icons.paymentList"
                          size="small"
                          @click="getListOfBills(item.id)"
                      />
                    </v-list-item>
                    <v-list-item>
                      <tooltip-button
                          :icon="true"
                          size="small"
                          color="primary"
                          :dusk="`return-${index}`"
                          data-text="Return Product"
                          @click="return_id=item.id"
                          :icon-name="$root.icons.hasReturn"
                      />
                    </v-list-item>
                    <v-list-item>
                      <tooltip-button
                          :icon="true"
                          size="small"
                          color="red"
                          data-text="Delete"
                          :dusk="`delete-${index}`"
                          @click="deleteItem(item.id)"
                          :icon-name="$root.icons.delete"
                      />
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>
            </v-data-table>
          </v-col>
        </v-row>


      </v-card-text>

      <list-print-bootstrap
          :columns="headers"
          :date_fields="['created_at', 'purchase_date']"
          :rows="items.data"
          :title="'Purchase List'"
          style="visibility: collapse"
      />
      <!--    <v-dialog scrollable v-model="createOrUpdateDialog">-->
      <create-purchase
          v-if="createOrUpdateDialog"
          v-model="createOrUpdateDialog"
          :model-id="edit_id"
          @returns-from-create="returnsFromCreate"
      />
      <show-purchase
          v-if="show_id"
          v-model="show_id"
      />
      <return-purchase
          v-if="return_id"
          v-model="return_id"
      ></return-purchase>
      <payment-crud
          model="purchase"
          v-if="payment_crud_dialog"
          :method="payment_crud_method"
          @paymentSuccess="paymentSuccess"
          :model_id="payment_crud_model_id"
          :payment_id="payment_crud_payment_id"
          v-model="payment_crud_dialog"
      />
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
              <!--                    @todo permission  -->
              <!--                    v-if="$options.filters.checkPermission(['bill_paid', 'show'])"-->
              <!--                    v-if="$options.filters.checkPermission(['purchase', 'edit'])"-->
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
                <!--                      <v-btn-->
                <!--                          text-->
                <!--                          icon-->
                <!--                          x-small-->
                <!--                          color="primary"-->
                <!--                          :to="{name:'bill.paid.show', params:{id:item.id}}"-->
                <!--                      >-->
                <!--                        <v-icon>mdi-eye</v-icon>-->
                <!--                      </v-btn>-->
              </template>
              <template v-slot:[`item.date`]="{item}">
                {{ item.date | removeTimeFromDate }}
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-dialog>

      <!--    </v-dialog>-->
      <!--    <unauthorize-message-->
      <!--        v-if="!$options.filters.checkPermission(['purchase', 'index'])"-->
      <!--    />-->

    </v-card>
  </v-container>
</template>
<script src="./index.js"></script>
<style scoped>
::v-deep .purchase-table th {
  padding: 0 3px !important;
}
</style>