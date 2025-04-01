<template>
  <v-dialog v-model="showModal">
    <v-form
        @submit.prevent="postPurchase()"
        id="create_purchase_dialog"
    >
      <v-card outlined :loading="loading">
        <v-card-title>
          {{ forms.id || modelId ? "Edit " : "Create " }}Purchase
          <template v-if="value">
            <v-spacer/>
            <v-btn icon @click="$emit('input', false)">
              <v-icon>{{ $root.icons.close }}</v-icon>
            </v-btn>
          </template>
        </v-card-title>
        <v-card-text>
          <v-row class="tw-bg-gray-100 px-4 py-4 rounded">
            <v-col md="3" cols="12">
              <vuetify-datetime
                  v-model="forms.purchase_date"
                  label="Purchase Date"
                  required
              />
            </v-col>
            <v-col md="3" cols="12">
              <v-text-field
                  dense
                  required
                  name="bill_no"
                  dusk="bill_no"
                  label="bill no"
                  data-vv-as="Bill no."
                  data-vv-name="bill_no"
                  v-model="forms.bill_no"
                  v-validate="{required:true}"
                  :error-messages="errors.collect('bill_no')"
              >
              </v-text-field>
            </v-col>
            <v-col md="3" cols="12">
              <v-autocomplete
                  dense
                  item-value="id"
                  :items="suppliers"
                  name="supplier_id"
                  dusk="supplier_id"
                  item-text="company"
                  data-vv-as="Supplier"
                  label="supplier company"
                  data-vv-name="supplier_id"
                  v-model="forms.supplier_id"
                  v-validate="{required:true}"
                  :error-messages="errors.collect('supplier_id')"
              >
                <template #append-outer>
                  <v-btn
                      fab
                      small
                      dark
                      color="success"
                      class="mt-n2 elevation-1"
                      @click="supplier_dialog=true"
                  >
                    <v-icon>
                      {{ $root.icons.create }}
                    </v-icon>
                  </v-btn>
                  <create-supplier
                      v-if="supplier_dialog"
                      v-model="supplier_dialog"
                      @getNewSupplier="getNewSupplier"
                  />
                </template>
              </v-autocomplete>
            </v-col>
            <v-col md="3" cols="12" id="status">
              <v-select
                  dense
                  name="status"
                  dusk="status"
                  label="status"
                  item-value="id"
                  item-text="name"
                  data-vv-as="Status"
                  data-vv-name="status"
                  v-model="forms.status"
                  v-validate="{required:true}"
                  :items="$store.state.delivery_statuses"
                  :error-messages="errors.collect('status')"
              />
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  dense
                  min="0"
                  required
                  step="any"
                  type="number"
                  name="shipping_cost"
                  dusk="shipping_cost"
                  :label="$root.shippingLabel"
                  v-model="forms.shipping_cost"
                  :error-messages="errors.collect('shipping_cost')"
              />
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  dense
                  min="0"
                  required
                  step="any"
                  type="number"
                  name="overall_discount"
                  dusk="overall_discount"
                  label="overall discount"
                  v-model="forms.overall_discount"
                  :error-messages="errors.collect('overall_discount')"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col md="12" cols="12">
              <v-autocomplete
                  dense
                  outlined
                  item-value="id"
                  label="product"
                  item-text="name"
                  :items="products"
                  v-model="product_id"
                  @change="addProduct"
                  dusk="search_product"
                  name="search_product"
                  :search-input.sync="searchProduct"
              >
                <template v-slot:item="data">
                  <v-list-item-content v-text="data.item.name"/>
                  <v-list-item-icon
                      v-text="data.item.code"
                      class="grey lighten-3"
                  />
                </template>
              </v-autocomplete>
            </v-col>
            <v-col cols="12" md="12" v-if="items.length > 0">
              <v-simple-table dense>
                <template v-slot:default>
                  <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Warehouse</th>
                    <th class="text-center">Unit</th>
                    <th class="text-center">{{ $store.state.quantity_label }}</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Discount</th>
                    <th class="text-center">adjustment</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">weight T.</th>
                    <th class="text-center">Action</th>
                  </tr>
                  </thead>
                  <tr v-for="(item, index) in items" :key="`product-${index}`">
                    <td style="cursor: grab">{{ index + 1 }}</td>
                    <td class="text-center">{{ item.pname }}</td>
                    <td>
                      <!--                    <warehouse-mapping-select v-model="item.location" :autocomplete="true"/>-->
                      <!--                    <small-->
                      <!--                        class="error--text"-->
                      <!--                        v-html="-->
                      <!--                          errors.collect('items.' + index + '.warehouse')-->
                      <!--                        "-->
                      <!--                    ></small>-->
                      <!--                    <small-->
                      <!--                        class="error--text"-->
                      <!--                        v-html="-->
                      <!--                          errors.collect('items.' + index + '.location')-->
                      <!--                        "-->
                      <!--                    ></small>-->
                      <v-autocomplete
                          dense
                          class="mx-1"
                          item-value="id"
                          item-text="name"
                          :items="warehouses"
                          data-vv-as="Warehouse"
                          v-model="item.warehouse"
                          data-vv-name="warehouse"
                          v-validate="{required:true}"
                          :error-messages="errors.collect('items.' + index + '.warehouse')"
                      />
                    </td>
                    <td>
                      <v-autocomplete
                          dense
                          class="mx-1"
                          item-value="id"
                          item-text="key"
                          data-vv-as="Unit"
                          data-vv-name="unit"
                          v-model="item.unit"
                          :items="item.units"
                          v-validate="{required:true}"
                          @change="getPrice(item.unit, index)"
                          :error-messages="errors.collect('items.' + index + '.unit')"
                      >
                        <template #append>
                          <conversion-component
                              :isPurchase="true"
                              :units="item.units"
                              :toUnit="parseInt(item.unit)"
                              :productId="item.product_id"
                              :componentId="tooltipId + index"
                              :baseUnit="parseInt(item.baseUnit)"
                              :quantity="parseFloat(item.quantity)"
                          />
                        </template>
                      </v-autocomplete>
                    </td>
                    <td>
                      <!--                      {{item.quantity}}-->
                      <v-text-field
                          dense
                          min="0"
                          step="any"
                          class="mx-1"
                          type="number"
                          data-vv-as="Quantity"
                          data-vv-name="quantity"
                          v-model="item.quantity"
                          v-validate="{required:true}"
                          :error-messages="errors.collect('items.' + index + '.quantity')"
                      />
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
                              <v-col
                                  md="2"
                                  :key="'par' + i"
                                  v-for="(n, i) in parseInt(item.quantity)"
                              >
                                {{ n }}
                                <v-text-field
                                    v-model="item.part_number[i].part_number"
                                    v-if="_.has(item.part_number[i], 'part_number')
"
                                >
                                </v-text-field>
                                <v-text-field
                                    v-else
                                    v-model="item.part_number[i]"
                                />
                              </v-col>
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
                          class="mx-1"
                          type="number"
                          data-vv-as="Unit Price"
                          data-vv-name="unit_price"
                          v-model="item.unit_price"
                          v-validate="{required:true}"
                          :error-messages="errors.collect('items.' + index + '.unit_price')"
                      />
                    </td>
                    <td>
                      <v-text-field
                          dense
                          min="0"
                          step="any"
                          class="mx-1"
                          type="number"
                          v-model="item.discount"
                      />
                    </td>
                    <td>
                      <v-text-field
                          dense
                          min="0"
                          step="any"
                          class="mx-1"
                          type="number"
                          v-model="item.adjustment"
                      />
                    </td>
                    <td class="text-center">
                      {{ getTotalPrice(item) }}
                    </td>
                    <td class="text-center">
                      {{ itemTotalWeight(index) }}
                    </td>
                    <td>
                      <v-btn
                          icon
                          @click="removeProduct(index)"
                          color="red"
                      >
                        <v-icon>{{ $root.icons.delete }}</v-icon>
                      </v-btn>
                    </td>
                  </tr>
                  <tr
                      v-for="item in forms.sum_fields"
                      v-if="forms.sum_fields !== undefined &&
                                  !_.isEmpty(forms.sum_fields) &&
                                  item.active"
                  >
                    <td colspan="8" class="text-right">
                      {{ item.label }}
                    </td>
                    <td colspan="1">
                      <v-text-field
                          type="number"
                          v-model="item.value"
                          class="text-center-for-v-text"
                      />
                    </td>
                    <td colspan="2"></td>
                  </tr>
                  <tr>
                    <td colspan="8" class="text-right">Grand Total:</td>
                    <td class="text-center" id="total">
                      {{ total }}
                    </td>
                    <td>
                      <v-text-field
                          label="add weight"
                          name="extra_weight"
                          v-model="extra_weight"
                          class="text-center-for-v-text"
                      />
                    </td>
                    <td colspan="2" class="pa-0">
                      <small>Weight T: {{ Number(forms.total_weight) + Number(extra_weight) }}</small>
                    </td>
                  </tr>
                  <tr v-show="axiosMethod !== 'patch'">
                    <td
                        colspan="8"
                        class="text-right"
                    >
                      Pay
                    </td>
                    <td>
                      <v-text-field
                          min="0"
                          step="any"
                          name="paid"
                          dusk="paid"
                          type="number"
                          @keyup="calDue"
                          v-model="forms.paid"
                          class="mx-1 text-center-for-v-text"
                          :error-messages="errors.collect('paid')"
                      />
                    </td>
                    <td>
                      <v-select
                          item-value="id"
                          item-text="name"
                          name="payment_type"
                          v-model="payment_type"
                          class="text-center-for-v-text"
                          :disabled="axiosMethod === 'patch'"
                          :items="$store.state.paymentMethods"
                          :error-messages="errors.collect('payment_type')"
                      >
                      </v-select>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <td
                        colspan="8"
                        class="text-right"
                    >
                      bank name
                    </td>
                    <td id="bank">
                      <v-select
                          name="bank"
                          dusk="bank"
                          class="mx-1"
                          :items="banks"
                          item-value="id"
                          item-text="name"
                          label="select bank"
                          v-model="forms.bank_id"
                          :disabled="axiosMethod === 'patch'"
                          :error-messages="errors.collect('bank_id')"
                      ></v-select>
                    </td>
                    <td
                        colspan="1"
                    >
                      <v-text-field
                          class="text-center-for-v-text"
                          label="transaction number"
                          :disabled="axiosMethod === 'patch'"
                          v-model="forms.transaction_number"
                      />
                      <v-text-field
                          class="text-center-for-v-text"
                          label="cheque number"
                          v-if="payment_type == 3"
                          :disabled="axiosMethod === 'patch'"
                          v-model="forms.cheque_number"
                      />
                    </td>
                    <td></td>
                  </tr>
                  <tr v-show="axiosMethod !== 'patch'">
                    <td colspan="7">
                      <v-textarea
                          dense
                          name="note"
                          label="note"
                          v-model="forms.note"
                      />
                    </td>
                    <td colspan="1" class="text-right">Due</td>
                    <td colspan="1" class="text-center">{{ parseFloat(due).toFixed(4) }}</td>
                    <td colspan="2" class="text-center">
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
                </template>
              </v-simple-table>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions v-if="items.length > 0">
          <v-btn
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
        <purchase-show-print
            :client_data="print_Data"
            style="visibility: collapse"
            :total_return_amount="print_totalRAmount"
            v-if="!_.isEmpty(print_Data) && !_.isEmpty(print_Data.supplier)"
        />
      </v-card>
    </v-form>
  </v-dialog>
</template>
<script src="./create.js"></script>
<style scoped>
tbody tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, .05);
}
</style>
