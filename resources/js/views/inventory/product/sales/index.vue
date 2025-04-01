<template>
  <v-container fluid>
    <v-card flat>
      <!--      <v-dialog v-model="exportDialog" width="800">-->
      <!--        <sale-export @closeDialogExport="closeDialogExport"/>-->
      <!--      </v-dialog>-->
      <v-card-title>
        Sales
        <v-spacer/>
        <action-btn
            dusk="create"
            @click="createOrUpdateDialog=true"
        />
        <collapse-btn @click="dense=!dense"/>
        <v-btn
            class="mr-1"
            color="success"
            outlined
            small
            text
            @click="payment_crud_dialog=true"
        >
          Payment
          <v-icon>{{ $root.icons.payment }}</v-icon>
        </v-btn>
        <v-btn
            color="success"
            outlined
            small
            text
            @click="isShowFilter=!isShowFilter"
        >
          {{ `${isShowFilter ? 'Hide Filter' : 'Show Filter'}` }}
          <v-icon>{{ `${isShowFilter ? $root.icons.filter : $root.icons.filterOff}` }}</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-row v-if="isShowFilter">
          <v-col>
            <v-select
                v-model="options.status"
                :items="$store.state.delivery_statuses"
                clearable
                hide-details
                item-text="name"
                item-value="id"
                label="status"
                @click.stop
            />
          </v-col>
          <v-col>
            <v-text-field
                v-model="options.biller"
                clearable
                hide-details
                label="biller"
                type="text"
            />
          </v-col>
          <v-col>
            <v-text-field
                v-model="options.ref"
                clearable
                hide-details
                label="ref"
                type="text"
                @click.stop
            />
          </v-col>
          <v-col>
            <v-text-field
                type="text"
                clearable
                hide-details
                label="customer"
                v-model="options.customer_name"
            >
            </v-text-field>
          </v-col>
          <v-col>
            <v-text-field
                clearable
                label="part number"
                v-model="options.part_number"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-data-table
            :dense="dense"
            :headers="headers"
            :loading="loading"
            :items="items.data"
            class="elevation-0 sale-table"
            :options.sync="options"
            :server-items-length="items.total"
            loading-text="Loading... Please wait"
            :footer-props="{
                itemsPerPageOptions: $store.state.itemsPerPageOptions,
              }"
        >
          <template v-slot:[`item.total_paid`]="{ item }">
            {{ item.total_paid | toFix(2) }}
          </template>
          <template v-slot:[`item.sales_date`]="{ item }">
            {{ item.sales_date | removeTimeFromDate }}
          </template>
          <template v-slot:[`item.due`]="{ item }">
            {{ item.due | toFix(2) }}
          </template>
          <template v-slot:item.status="{ item }">
            {{ $root.productStatus(item.status) }}
          </template>
          <template v-slot:item.action="{ item, index }">
            <v-menu top :close-on-content-click="closeOnContentClick">
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
                <v-list-item
                >
                  <tooltip-button
                      :icon="true"
                      size="small"
                      color="primary"
                      data-text="View"
                      icon-name="mdi-eye"
                      @click="show_id = item.id"
                  />
                </v-list-item>
                <v-list-item>
                  <tooltip-button
                      icon
                      size="small"
                      color="primary"
                      data-text="Edit"
                      :dusk="`edit-${index}`"
                      :icon-name="$root.icons.edit"
                      @click="edit_id=item.id, createOrUpdateDialog=true"
                  />
                </v-list-item>
                <v-list-item>
                  <tooltip-button
                      icon
                      color="red"
                      size="small"
                      data-text="Delete"
                      :dusk="`delete-${index}`"
                      @click="deleteItem(item.id)"
                      :icon-name="$root.icons.delete"
                  />
                </v-list-item>
                <v-list-item>
                  <tooltip-button
                      :icon-name="$root.icons.payment"
                      color="success"
                      data-text="Payment"
                      icon
                      size="small"
                      :dusk="`payment-${index}`"
                      @click="createPaymentById(item.id)"
                  />
                </v-list-item>
                <v-list-item>
                  <tooltip-button
                      icon
                      data-text="Payment List"
                      :icon-name="$root.icons.paymentList"
                      size="small"
                      color="primary"
                      @click="getListOfPayments(item.id)"
                  />
                </v-list-item>
                <v-list-item>
                  {{ item.created_at }}
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
    <!--    <v-dialog-->
    <!--        scrollable-->
    <!--        v-model="createOrUpdateDialog"-->
    <!--    >-->
    <create-sale
        :model-id="edit_id"
        v-if="createOrUpdateDialog"
        v-model="createOrUpdateDialog"
        @returns-from-create="returnsFromCreate"
    />
    <show-purchase
        v-if="show_id"
        v-model="show_id"
        @returns-from-create="returnsFromCreate"
    />
    <!--    </v-dialog>-->
    <list-print-bootstrap
        :title="'Sales List'"
        :columns="headers"
        :rows="items.data"
        style="visibility: collapse"
        :date_fields="['sales_date', 'created_at']"
    />
    <payment-crud
        model="sale"
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
    <!--    <unauthorize-message-->
    <!--        v-if="!$options.filters.checkPermission(['sales', 'index'])"-->
    <!--    />-->
  </v-container>
</template>
<script src="./index.js"></script>
<style scoped>
::v-deep .sale-table th {
  padding: 0 3px !important;
}
</style>