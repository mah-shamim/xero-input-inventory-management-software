<script src="./show.js"></script>
<template>
  <v-dialog v-model="showModal">
    <v-card>
      <payment-crud
          model="purchase"
          v-if="payment_crud_dialog"
          :method="payment_crud_method"
          @paymentSuccess="paymentSuccess"
          :model_id="value"
          :payment_id="payment_crud_payment_id"
          v-model="payment_crud_dialog"
      />
      <!--    <v-dialog-->
      <!--        persistent-->
      <!--        v-model="dialog"-->
      <!--        max-width="200px"-->
      <!--    >-->
      <!--      <v-card>-->
      <!--        <v-card-text>-->
      <!--          <v-container>-->
      <!--            <v-row>-->
      <!--              <v-col cols="12" md="12">-->
      <!--                <v-text-field-->
      <!--                    required-->
      <!--                    type="number"-->
      <!--                    label="amount"-->
      <!--                    v-model="forms.price"-->
      <!--                ></v-text-field>-->
      <!--              </v-col>-->
      <!--              <v-col>-->
      <!--                <v-btn-->
      <!--                    text-->
      <!--                    x-small-->
      <!--                    color="blue darken-1"-->
      <!--                    @click="dialog = false"-->
      <!--                >-->
      <!--                  Close-->
      <!--                </v-btn>-->
      <!--                <v-btn-->
      <!--                    text-->
      <!--                    x-small-->
      <!--                    color="blue darken-1"-->
      <!--                    @click="updateReturn()"-->
      <!--                >-->
      <!--                  Save-->
      <!--                </v-btn>-->
      <!--              </v-col>-->
      <!--            </v-row>-->
      <!--          </v-container>-->
      <!--        </v-card-text>-->
      <!--      </v-card>-->
      <!--    </v-dialog>-->
      <v-card-title>
        Purchase Ledger: {{ purchase.bill_no }}
        <v-spacer></v-spacer>
        <!--      v-if="$options.filters.checkPermission(['purchase', 'return'])"-->
        <v-btn
            @click="return_id=value"
            color="warning"
            outlined
            x-small
            class="mr-1"
        >
          <v-icon>{{ $root.icons.return }}</v-icon>
          Return
        </v-btn>
        <v-btn
            color="success"
            outlined
            x-small
            @click="createBill()"
        >
          Pay Bill
        </v-btn>
        <v-btn
            fab
            icon
            x-small
            @click="$htmlToPaper('myPrint')"
        >
          <v-icon>mdi-printer</v-icon>
        </v-btn>
        <v-btn icon fab x-small>
          <v-icon>mdi-email</v-icon>
        </v-btn>
        <v-btn icon fab x-small>
          <v-icon>mdi-pdf-box</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col md="4" cols="12">
            <p class="text-subtitle-1">Supplier Information</p>
            <p>name: {{ purchase.supplier.name }}</p>
            <p>company: {{ purchase.supplier.company }}</p>
            <p>email: {{ purchase.supplier.email }}</p>
            <p>phone: {{ purchase.supplier.phone }}</p>
            <p>address: {{ purchase.supplier.address }}</p>

          </v-col>
          <v-col md="4" cols="12">
            <p class="text-subtitle-1">Shop Information</p>
            <p>Company name: {{ company.name }}</p>
            <p>Address Detail: {{ company.address1 }}</p>
            <div>
              <p>
                mobile number:
                <span
                    v-for="(phone, index) in company.contact_phone"
                >
                  {{ phone }}{{ company.contact_phone.length - 1 === index ? '' : ',' }}
              </span>
              </p>
            </div>
          </v-col>
          <v-col md="4" cols="12">
            <p class="text-subtitle-1">Purchase Information</p>
            <p>Bill no: {{ purchase.bill_no }}</p>
            <p>Bill Date: {{ purchase.purchase_date | removeTimeFromDate }}</p>
            <p>created At: {{ purchase.created_at }}</p>
          </v-col>
        </v-row>
        <v-row>
          <v-col md="8" cols="12">
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Code</th>
                  <th>Name(qty)</th>
                  <th>{{ $store.state.quantity_label }}</th>
                  <th>Unit Cost</th>
                  <th>Discount</th>
                  <th class="bg-warning-lt">Total</th>
                  <th>status</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(product, index) in purchase.products" :key="index">
                  <td>
                    {{ index + 1 }}
                  </td>
                  <td>{{ product.code }}</td>
                  <td>{{ product.name }}</td>
                  <td>
                    {{ product.quantityStr }}({{ parseFloat(product.pivot.quantity).toFixed(2) }})
                    <template v-if="product.manufacture_part_number">
                      <br>
                      part number:
                      <span
                          :key="'pn'+i"
                          v-for="(n, i) in $options.filters.sortPartNumber(
                            purchase.partnumbers,
                            product,
                            product.pivot.warehouse_id)"
                      >
                       {{ n.part_number }},
                    </span>
                    </template>
                  </td>
                  <td>{{ product.pivot.price }}</td>
                  <td>{{ product.pivot.discount }}</td>
                  <td class="bg-warning-lt strong">{{ product.pivot.subtotal }}</td>
                  <td>{{ $root.productStatus(purchase.status) }}</td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                  <td colspan="6" class="text-right">Product Total</td>
                  <td colspan="2">{{ product_total }}</td>
                </tr>
                <tr class="strong">
                  <td colspan="6" class="text-right">{{ $root.shippingLabel }}</td>
                  <td
                      colspan="2"
                      class="text-left bg-success"
                      v-if="purchase.shipping_cost"
                  >
                    {{ purchase.shipping_cost }}
                  </td>
                  <td colspan="2" class="text-left bg-success" v-else>0</td>
                </tr>
                <tr>
                  <td colspan="6" class="text-right">
                    Sub Total
                  </td>
                  <td colspan="2">
                    {{ sub_total }}
                  </td>
                </tr>
                <tr class="strong">
                  <td colspan="6" class="text-right">overall discount</td>
                  <td colspan="2" class="text-left bg-info" v-if="purchase.overall_discount">
                    {{ purchase.overall_discount }}%({{ discounted_amount }})
                  </td>
                  <td colspan="2" class="text-left bg-info" v-else>0 %</td>
                </tr>
                <tr
                    class="strong"
                    v-for="item in purchase.sum_fields"
                    v-if="purchase.sum_fields!==undefined && purchase.sum_fields.length>0"
                >
                  <td colspan="6" class="text-right">{{ item.label }}</td>
                  <td colspan="2" class="text-left bg-success">{{ item.value }}</td>
                </tr>
                <tr class="strong">
                  <td colspan="6" class="text-right">Total</td>
                  <td
                      colspan="2"
                      class="text-left bg-primary"
                  >
                    {{ purchase.total }}
                  </td>
                </tr>
                </tfoot>
              </template>
            </v-simple-table>
          </v-col>
          <v-col md="4" cols="12">
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th>Method</th>
                  <th>Date</th>
                  <th>Amount paid</th>
                  <th>action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="payment in purchase.payments">
                  <td v-if="payment.transaction">
                    <router-link :to="{name:'bank.transaction', params:{id:payment.transaction.id}}">
                      {{ $root.paymentMethods(payment.payment_type) }}
                    </router-link>
                  </td>
                  <td v-else>
                    {{ $root.paymentMethods(payment.payment_type) }}
                  </td>
                  <td>{{ payment.date | removeTimeFromDate }}</td>
                  <td class="strong">{{ payment.paid }}</td>
                  <td>
                    <v-btn
                        text
                        icon
                        x-small
                        color="success"
                        @click="editBill(payment.id)"
                    >
                      <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                  </td>
                </tr>
                <tr>
                  <td class="text-right" colspan="2">
                    {{
                      $root.$data.erp.report.checkPaymentStatus(purchase.total,
                          purchase.payments) < 0
                          ? 'Due' : $root.$data.erp.report.checkPaymentStatus(purchase.total, purchase.payments) == 0
                              ? 'Due' : $root.$data.erp.report.checkPaymentStatus(purchase.total, purchase.payments) > 0
                                  ? 'Adjustment Paid'
                                  : ''
                    }}
                  </td>
                  <td class="text-left strong">
                    {{
                      parseFloat($root.$data.erp.report.checkPaymentStatus(purchase.total, purchase.payments)).toFixed(4)
                    }}
                  </td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="12" v-if="purchase.returns.length>0">
            <p class="text-center">Product Return detail</p>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Code</th>
                  <th>Name</th>
                  <th>{{ $store.state.quantity_label }}</th>
                  <th>Unit</th>
                  <th>Amount</th>
                  <!--                  <th>Action</th>-->
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, index) in purchase.returns">
                  <td>
                    {{ index + 1 }}
                  </td>
                  <td>{{ item.product.code }}</td>
                  <td>{{ item.product.name }}</td>
                  <td>{{ parseFloat(item.quantity).toFixed(4) }}</td>
                  <td>{{ item.unit.key }}</td>
                  <td class="bg-warning-lt strong">{{ item.amount }}</td>
                  <!--                  <td>-->
                  <!--                    <v-btn x-small @click="showAmountEdit(item, index)">edit</v-btn>-->
                  <!--                  </td>-->
                </tr>
                </tbody>
                <tfoot>
                <tr class="strong">
                  <td colspan="5" class="text-right">Total</td>
                  <td colspan="1" class="text-left bg-info-lt">
                    {{ totalRAmount }}
                    <!--                    @todo will fix when account transaction will live-->
                    <!--                    <router-link-->
                    <!--                        :to="{name:'bank.transaction', params:{id:purchase.returns[0].transaction_id}}">-->
                    <!--                      {{ totalRAmount }}-->
                    <!--                    </router-link>-->
                  </td>
                </tr>
                </tfoot>
              </template>
            </v-simple-table>
          </v-col>
        </v-row>
        <v-row>
          <v-col md="6">
            <div v-if="purchase.note">
              <p>Note:</p>
              <p>{{ purchase.note }}</p>
            </div>
          </v-col>

        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-btn
            outlined
            text
            @click="$emit('input', null)"
        >
          Close
        </v-btn>
      </v-card-actions>
      <return-purchase
          v-model="return_id"
          v-if="return_id"
      />
          <purchase-show-print
              :client_data="purchase"
              v-if="!_.isEmpty(purchase)"
              style="visibility: collapse"
              :total_return_amount="totalRAmount"
          >
          </purchase-show-print>
    </v-card>
  </v-dialog>
</template>