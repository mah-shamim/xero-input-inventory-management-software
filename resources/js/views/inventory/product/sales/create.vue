<template>
  <v-dialog v-model="showModal">
    <v-form
        @submit.prevent="postSales"
        id="create_sale_dialog"
    >
      <v-card outlined :loading="loading">
        <v-card-title>
          {{ sales.id ? "Edit " : "Create " }}Sale
          <v-spacer/>
          <v-btn
              icon
              v-if="value"
              @click="$emit('input', false)"
          >
            <v-icon>{{ $root.icons.close }}</v-icon>
          </v-btn>
        </v-card-title>
        <v-divider/>
        <v-card-text class="fill-height">
          <v-row>
            <v-col md="3" cols="12">
              <vuetify-datetime
                  v-model="forms.sales_date"
                  label="Sale Date"
                  name="sales_date"
                  required
              />
            </v-col>
            <v-col md="3" cols="12">
              <v-autocomplete
                  v-model="forms.customer_id"
                  v-validate="{required:true}"
                  :error-messages="errors.collect('customer_id')"
                  :items="customers"
                  data-vv-as="Customer"
                  data-vv-name="customer_id"
                  dense
                  dusk="customer_id"
                  item-text="name"
                  item-value="id"
                  label="customer"
                  name="customer_id"
                  @change="getCustomerDue"
              >
                <template #append-outer>
                  <v-dialog v-model="dialog" width="900">
                    <template v-slot:activator="{ on:dialog }">
                      <v-tooltip top>
                        <template #activator="{on:tooltip}">
                          <v-btn
                              fab
                              dark
                              small
                              color="success"
                              class="mt-n2 elevation-1"
                              v-on="{...tooltip, ...dialog}"
                          >
                            <v-icon>
                              {{ $root.icons.create }}
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>Create Customer</span>
                      </v-tooltip>
                    </template>
                    <create-customer
                        v-if="dialog"
                        v-model="dialog"
                        @updateCustomerList="updateCustomerList"
                    />
                  </v-dialog>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col md="3" cols="12" id="status">
              <v-select
                  v-model="forms.status"
                  v-validate="{required:true}"
                  :error-messages="errors.collect('status')"
                  :items="$store.state.delivery_statuses"
                  data-vv-as="Status"
                  data-vv-name="status"
                  dense
                  dusk="status"
                  item-text="name"
                  item-value="id"
                  label="status"
                  name="status"
              />
            </v-col>
            <v-col md="3" cols="12">
              <v-text-field
                  v-model="forms.shipping_cost"
                  :error-messages="errors.collect('shipping_cost')"
                  :label="$root.shippingLabel"
                  dense
                  min="0"
                  name="shipping_cost"
                  dusk="shipping_cost"
                  required
                  step="any"
                  type="number"
              />
            </v-col>
            <v-col md="3" cols="12">
              <v-text-field
                  v-model="forms.overall_discount"
                  :error-messages="errors.collect('overall_discount')"
                  dense
                  label="overall discount"
                  min="0"
                  name="overall_discount"
                  dusk="overall_discount"
                  required
                  step="any"
                  type="number"
              />
            </v-col>
            <v-col md="3" cols="12">
              <v-textarea
                  dense
                  outlined
                  label="note"
                  v-model="forms.note"
              />
            </v-col>
            <v-col
                md="3"
                cols="12"
                v-if="$root.isProfitPercent"
            >
              <v-checkbox
                  dense
                  outlined
                  v-model="sale_profit"
                  label="sales on profit"
                  @click="getInputField($event)"
                  v-if="$root.isProfitPercent"
              />
            </v-col>
            <v-col md="12" cols="12">
              <v-autocomplete
                  v-model="product_id"
                  :disabled="axiosMethod === 'patch'"
                  :items="products"
                  :search-input.sync="searchProduct"
                  dusk="search_product"
                  name="search_product"
                  dense
                  item-text="name"
                  item-value="id"
                  label="product"
                  outlined
                  @change="(event)=>getClickedProduct(event)"
              >
                <template v-slot:item="data">
                  <v-list-item-content v-text="data.item.name">
                  </v-list-item-content>
                  <v-list-item-icon
                      class="grey lighten-3"
                      v-text="data.item.code"
                  >
                  </v-list-item-icon>
                </template>
              </v-autocomplete>
            </v-col>
            <v-col md="12" cols="12" v-if="items.length > 0">
              <v-simple-table dense>
                <template v-slot:default>
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Warehouse</th>
                    <th>Unit</th>
                    <th>{{ $store.state.quantity_label }}</th>
                    <th>Price</th>
                    <th v-if="$root.isProfitPercent">Profit (%)</th>
                    <th>Discount</th>
                    <th>Adjustment</th>
                    <th>Total</th>
                    <th>weight T.</th>
                    <th>action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(item, index) in items" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>{{ item.pname }}</td>
                    <td>
                      <!--                      <warehouse-mapping-select-->
                      <!--                          :autocomplete="true"-->
                      <!--                          v-model="item.location"-->
                      <!--                          :product-id="item.product_id"-->
                      <!--                      />-->
                      <!--                      <small-->
                      <!--                          class="error&#45;&#45;text"-->
                      <!--                          v-html="-->
                      <!--                          errors.collect('items.' + index + '.warehouse')-->
                      <!--                        "-->
                      <!--                      />-->
                      <!--                      <small-->
                      <!--                          class="error&#45;&#45;text"-->
                      <!--                          v-html="-->
                      <!--                          errors.collect('items.' + index + '.location')-->
                      <!--                        "-->
                      <!--                      />-->
                      <v-autocomplete
                          v-model="item.warehouse"
                          :disabled="axiosMethod === 'patch'"
                          :error-messages="errors.collect('items.' + index + '.warehouse')"
                          :items="item.warehouses"
                          dense
                          item-text="name"
                          item-value="id"
                          data-vv-name="warehouse"
                          v-validate="{required:true}"
                          data-vv-as="Warehouse"
                          @change="item.selected_part_number=[]"
                      />
                    </td>
                    <td>
                      <v-autocomplete
                          v-model="item.unit"
                          :error-messages="errors.collect('items.' + index + '.unit')"
                          :items="item.units"
                          dense
                          item-text="key"
                          item-value="id"
                          data-vv-as="Unit"
                          data-vv-name="unit"
                          v-validate="{required:true}"
                          @change="getPrice(item.unit, index)"
                      >
                        <template #append-outer>
                          <conversion-component
                              :baseUnit="parseInt(item.baseUnit)"
                              :componentId="tooltipId + index"
                              :quantity="parseFloat(item.quantity)"
                              :toUnit="item.unit"
                              :units="item.units"
                          />
                        </template>
                      </v-autocomplete>
                    </td>
                    <td>
                      <v-text-field
                          dense
                          min="0"
                          step="any"
                          type="number"
                          v-validate="{required:true}"
                          data-vv-as="Quantity"
                          data-vv-name="quantity"
                          v-model="item.quantity"
                          :error-messages="errors.collect('items.' + index + '.quantity')"
                      >
                        <template #append-outer>
                          <v-chip small>
                            {{ parseFloat(item.productInStock).toFixed(2) }}
                          </v-chip>
                        </template>
                      </v-text-field>
                      <v-dialog
                          width="800"
                          v-model="part_number_dialog"
                          v-if="Number(item.manufacture_part_number) && item.quantity"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                              text
                              x-small
                              v-on="on"
                              v-bind="attrs"
                              color="success"
                          >
                            add part number {{ item.quantity }}
                          </v-btn>
                        </template>
                        <v-card>
                          <v-card-title>
                            add Part Number of {{ item.quantity }} items
                          </v-card-title>

                          <v-card-text>
                            <v-row>
                              <v-select
                                  multiple
                                  item-value="id"
                                  item-text="part_number"
                                  v-model="item.selected_part_number"
                                  :items="sortByWarehouse(item.part_number, item.warehouse)"
                                  :item-disabled="checkSelected(item.part_number, item.selected_part_number, item.quantity)"
                              />
                            </v-row>
                          </v-card-text>
                          <v-divider/>
                          <v-card-actions>
                            <v-spacer/>
                            <v-btn
                                text
                                color="primary"
                                @click="part_number_dialog = false"
                            >
                              submit
                            </v-btn>
                          </v-card-actions>
                        </v-card>
                      </v-dialog>
                    </td>
                    <td>
                      <v-text-field
                          dense
                          min="0"
                          step="any"
                          type="number"
                          v-model="item.unit_price"
                          data-vv-as="Unit Price"
                          data-vv-name="unit_price"
                          v-validate="{required:true}"
                          :error-messages="
                            errors.collect('items.' + index + '.unit_price')
                          "
                      />
                    </td>
                    <td v-if="profitPercentageField == true">
                      <v-text-field
                          dense
                          min="0"
                          step="any"
                          type="number"
                          v-model="item.sales_profit"
                      />
                    </td>
                    <td>
                      <v-text-field
                          dense
                          min="0"
                          step="any"
                          type="number"
                          v-model="item.discount"
                      />
                    </td>
                    <td>
                      <v-text-field
                          dense
                          min="0"
                          step="any"
                          type="number"
                          v-model="item.adjustment"
                      />
                    </td>
                    <td>{{ getTotalPrice(item) }}</td>
                    <td>
                      {{ itemTotalWeight(index) }}
                    </td>
                    <td>
                      <v-btn
                          icon
                          dark
                          color="red"
                          :disabled="axiosMethod === 'patch'"
                          @click="removeProduct(index)"
                      >
                        <v-icon>{{ $root.icons.delete }}</v-icon>
                      </v-btn>
                    </td>
                  </tr>

                  </tbody>
                  <tfoot>
                  <tr>
                    <td>{{ items.length }}</td>
                    <td colspan="7" class="text-right">Grand Total:</td>
                    <td id="total">{{ total }}</td>
                    <td>
                      <div class="d-flex justify-center align-center">
                        <v-text-field
                            type="number"
                            label="add weight"
                            name="extra_weight"
                            v-model="extra_weight"
                        />
                        Total: {{ Number(forms.total_weight) + Number(extra_weight) }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-right dark" colspan="8">Previous Due:</td>
                    <td class="dark">
                      {{ parseFloat(previousDue).toFixed(4) }}
                    </td>
                    <td></td>
                  </tr>
                  <tr v-if="axiosMethod !== 'patch'">
                    <td colspan="8" class="text-right">Pay</td>
                    <td>
                      <v-text-field
                          min="0"
                          step="any"
                          type="number"
                          @keyup="calDue"
                          dusk="paid"
                          v-model="forms.paid"
                      />
                    </td>
                    <td>
                      <v-select
                          item-value="id"
                          item-text="name"
                          name="payment_type"
                          v-model="forms.payment_type"
                          :disabled="axiosMethod === 'patch'"
                          :items="$store.state.paymentMethods"
                          :error-messages="errors.collect('payment_type')"
                      />
                    </td>
                  </tr>
                  <tr v-if="forms.payment_type == 2 || forms.payment_type == 3">
                    <td colspan="8" class="text-right">bank name</td>
                    <td id="bank">
                      <v-select
                          dusk="bank"
                          name="bank"
                          :items="banks"
                          item-value="id"
                          item-text="name"
                          label="select bank"
                          :disabled="forms.id"
                          v-model="forms.bank_id"
                          :error-messages="errors.collect('bank_id')"
                      />
                    </td>
                    <td>
                      <v-text-field
                          label="transaction number"
                          :disabled="axiosMethod === 'patch'"
                          v-model="forms.transaction_number"
                      />
                      <v-text-field
                          label="cheque number"
                          v-if="forms.payment_type == 3"
                          :disabled="axiosMethod === 'patch'"
                          v-model="forms.cheque_number"
                      />
                    </td>
                  </tr>
                  <tr v-if="axiosMethod !== 'patch'">
                    <td class="text-right" colspan="8">Due:</td>
                    <td class="dark">{{ parseFloat(due).toFixed(4) }}</td>
                    <td
                    >
                      <v-chip
                          small
                          :color="`${
                                      payment_status === 1
                                        ? 'success'
                                        : payment_status === 2
                                        ? 'warning'
                                        : 'danger'
                                      }`"
                      >
                        {{
                          payment_status === 1
                              ? "paid"
                              : payment_status === 2
                                  ? "partial"
                                  : "due"
                        }}
                      </v-chip>
                    </td>
                  </tr>
                  </tfoot>
                </template>
              </v-simple-table>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider/>
        <v-card-actions v-if="items.length > 0">
          <v-btn
              text
              outlined
              type="submit"
              value="submit"
              color="success"
              dusk="submit-new"
              :loading="loading"
              v-if="value && !modelId"
          >
            {{ "Submit & New" }}
            <v-icon class="mr-1">
              {{ $root.icons.submitButton.new }}
            </v-icon>
          </v-btn>
          <v-btn
              text
              outlined
              type="submit"
              color="primary"
              :loading="loading"
              value="submit-close"
              dusk="submit-close"
              v-if="value && axiosMethod === 'post'"
          >
            {{ forms.id ? "Update" : "Submit & Close" }}
            <v-icon class="mr-1">{{ $root.icons.submitButton.new }}</v-icon>
          </v-btn>
          <template v-if="axiosMethod === 'post'">
            <v-btn
                text
                outlined
                type="submit"
                value="print"
                color="warning"
                :loading="loading"
                dusk="submit-print"
            >
              {{ forms.id ? "Update" : "Submit & Print" }}
              <v-icon class="mr-1">
                {{ $root.icons.submitButton.print }}
              </v-icon>
            </v-btn>
            <v-btn
                text
                :loading="loading"
                color="primary"
                dusk="submit-view"
                outlined
                type="submit"
                value="view"
            >
              {{ forms.id ? "Update" : "Submit & View" }}
              <v-icon class="mr-1">
                {{ $root.icons.submitButton.view }}
              </v-icon>
            </v-btn>
          </template>
          <v-btn
              text
              outlined
              dusk="update"
              v-if="axiosMethod === 'patch'"
              :loading="loading"
              color="success"
              type="submit"
              value="update"
          >
            Update
          </v-btn>
          <v-btn
              v-if="!forms.id && !value"
              color="warning"
              @click="onCancel"
          >
            <v-icon class="mr-1">
              {{ $root.icons.reload }}
            </v-icon>
            Reset
          </v-btn>
          <v-btn
              v-if="value"
              tabindex="12"
              text
              @click="$emit('input', false)"
          >
            Close
          </v-btn>
          <v-btn v-if="!value" color="primary" @click="$router.back()">
            <v-icon class="mr-1">
              {{ $root.icons.return }}
            </v-icon>
            back
          </v-btn>
        </v-card-actions>
        <sale-show-print
            :client_data="print_data"
            style="visibility: collapse"
            v-if="!_.isEmpty(print_data)"
        />
      </v-card>
    </v-form>
  </v-dialog>
</template>

<script src="./create.js"></script>
