<template>
  <v-dialog v-model="showModal">
    <v-card>
      <payment-crud
          model="sale"
          v-if="payment_crud_dialog"
          :method="payment_crud_method"
          @paymentSuccess="paymentSuccess"
          :model_id="value"
          :payment_id="payment_crud_payment_id"
          v-model="payment_crud_dialog"
      />
      <!--    <v-dialog v-model="dialog" persistent max-width="200px">-->
      <!--      <v-card>-->
      <!--        <v-card-text>-->
      <!--          <v-container>-->
      <!--            <v-row>-->
      <!--              <v-col cols="12" md="12">-->
      <!--                <v-text-field-->
      <!--                    label="amount"-->
      <!--                    type="number"-->
      <!--                    v-model="forms.price"-->
      <!--                    required-->
      <!--                ></v-text-field>-->
      <!--              </v-col>-->
      <!--              <v-col>-->
      <!--                <v-btn x-small color="blue darken-1" text @click="dialog = false">Close</v-btn>-->
      <!--                <v-btn x-small color="blue darken-1" text @click="updateReturn()">Save</v-btn>-->
      <!--              </v-col>-->
      <!--            </v-row>-->
      <!--          </v-container>-->
      <!--        </v-card-text>-->
      <!--      </v-card>-->
      <!--    </v-dialog>-->

      <v-card-title>
        Sale Ledger of ref: {{ sales.ref }}
        <v-spacer></v-spacer>
        <!--            <v-btn-->
        <!--                    text-->
        <!--                    small-->
        <!--                    type="button"-->
        <!--                    color="primary"-->
        <!--                    onclick="printJS({printable: 'myPrint', type: 'html', showModal: false  })"-->
        <!--            >Print-->
        <!--            </v-btn>-->
        <!--      v-if="$options.filters.checkPermission(['sales', 'edit'])"-->
        <v-btn
            text
            outlined
            small
            class="mr-1"
            color="success"
            @click="$emit('returns-from-create', 'edit', sales.id)"
        >
          edit
        </v-btn>
        <!--      v-if="$options.filters.checkPermission(['sales', 'return'])"-->
<!--        <v-btn-->
<!--            text-->
<!--            small-->
<!--            color="success"-->
<!--            :to="{name:'sale.return',param:{id:sales.id}}"-->
<!--        >return-->
<!--        </v-btn>-->
        <!--      v-if="$options.filters.checkPermission(['sales', 'create'])"-->
        <v-btn
            text
            outlined
            small
            color="success"
            @click="createPayment()"
        >
          Pay Bill
        </v-btn>
        <v-btn
            icon
            fab
            x-small
            @click="$htmlToPaper('printSale')">
          <v-icon>mdi-printer</v-icon>
        </v-btn>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <v-row>
          <v-col md="4" cols="12">
            <p class="text-subtitle-1"><strong>Customer Information</strong></p>
            <p class="text-caption">name: {{ sales.customer.name }}</p>
            <p class="text-caption">email: {{ sales.customer.email }}</p>
            <p class="text-caption" v-if="sales.customer.phone">phone: {{ sales.customer.phone }}</p>
            <p class="text-caption" v-if="sales.customer.address">address: {{ sales.customer.address }}</p>
          </v-col>
          <v-col md="4" cols="12">
            <p class="text-subtitle-1"><strong>Shop Information</strong></p>
            <p class="text-caption">Company name: {{ company.name }}</p>
            <p class="text-caption">Address Detail: {{ company.address1 }}</p>
            <p class="text-caption">
              mobile number:
              <span v-for="(phone, index) in company.contact_phone">
                {{ phone }}{{ company.contact_phone.length - 1 == index ? '' : ',' }}
            </span>
            </p>
          </v-col>
          <v-col md="4" cols="12">
            <p class="text-subtitle-1"><strong>Sales Information</strong></p>
            <p class="text-caption">Ref: {{ sales.ref }}</p>
            <p class="text-caption">Status: {{ sales.status|delivery_status }}</p>
            <p class="text-caption">Sales Date: {{ sales.sales_date | removeTimeFromDate }}</p>
            <p class="text-caption">created At: {{ sales.created_at }}</p>
          </v-col>
        </v-row>
        <v-row>
          <v-col md="8" cols="12">
            <v-simple-table dense>
              <template v-slot:default>
                <thead>
                <tr>
                  <th>No.</th>
                  <th>Code</th>
                  <th>Name</th>
                  <th>{{ $store.state.quantity_label }}</th>
                  <th>Unit Cost</th>
                  <th>Discount</th>
                  <th>adjustment</th>
                  <th>Total</th>
                  <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(product, index) in sales.products" :index="index">
                  <td>
                    {{ index + 1 }}
                  </td>
                  <td>{{ product.code }}</td>
                  <td>{{ product.name }}</td>
                  <td>
                    {{ product.quantityStr }}({{ parseFloat(product.pivot.quantity).toFixed(4) }})
                    <template v-if="product.manufacture_part_number">
                      <br>
                      part number:
                      <span
                          :key="'pn'+i"
                          v-for="(n, i) in $options.filters.sortPartNumber(
                            sales.partnumbers,
                            product,
                            product.pivot.warehouse_id)"
                      >
                       {{ n.part_number }},
                    </span>
                    </template>
                  </td>
                  <td>{{ product.pivot.price }}</td>
                  <td>{{ product.pivot.discount }}</td>
                  <td>{{product.pivot.adjustment}}</td>
                  <td>{{ product.pivot.subtotal }}</td>
                  <td>{{ $root.productStatus(sales.status) }}</td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                  <td colspan="7" class="text-right">Product Total</td>
                  <td colspan="2">{{ product_total }}</td>
                </tr>
                <tr class="strong">
                  <td colspan="7" class="text-right">{{ $root.shippingLabel }}</td>
                  <td colspan="2" v-if="sales.shipping_cost">{{ sales.shipping_cost }}</td>
                  <td colspan="2" v-else>0</td>
                </tr>

                <tr>
                  <td colspan="7" class="text-right">
                    Sub Total
                  </td>
                  <td colspan="2">
                    {{ sub_total }}
                  </td>
                </tr>

                <tr class="strong">
                  <td colspan="7" class="text-right">Overall Discount</td>
                  <td colspan="2" v-if="sales.overall_discount">
                    {{ sales.overall_discount }}%({{ discounted_amount }})
                  </td>
                  <td colspan="2" v-else>0</td>
                </tr>
                <tr class="strong">
                  <td colspan="7" class="text-right">Total</td>
                  <td colspan="2">{{ sales.total }}</td>
                </tr>
                <tr class="strong">
                  <td colspan="7" class="text-right">Previous Due</td>
                  <td colspan="2">{{ sales.previous_due }}</td>
                </tr>
                <tr class="strong">
                  <td colspan="7" class="text-right">Grand Total</td>
                  <td colspan="2">
                    {{ (parseFloat(sales.total) + parseFloat(sales.previous_due)).toFixed(4) }}
                  </td>
                </tr>
                </tfoot>
              </template>
            </v-simple-table>
          </v-col>
          <v-col md="4" cols="12">
            <v-simple-table dense>
              <template v-slot:default>
                <thead>
                <tr>
                  <th>Method</th>
                  <th>Date</th>
                  <th>Amount paid</th>
                  <!--                v-if="$options.filters.checkPermission(['sales', 'edit'])"-->
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="payment in sales.payments">
                  <td v-if="payment.transaction">
                    <router-link
                        :to="{name:'bank.transaction', params:{id:payment.transaction.id}}"
                    >
                      <v-badge>
                        {{ $root.paymentMethods(payment.payment_type) }}
                      </v-badge>
                    </router-link>
                  </td>
                  <td v-else>
                    {{ $root.paymentMethods(payment.payment_type) }}
                  </td>
                  <td>{{ payment.date | removeTimeFromDate }}</td>
                  <td class="strong">{{ payment.paid }}</td>
                  <!--                v-if="$options.filters.checkPermission(['sales', 'edit'])"-->
                  <td>
                    <v-btn
                        text
                        icon
                        x-small
                        color="success"
                        @click="editPayment(payment.id)"
                    >
                      <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                  </td>
                </tr>
                <tr>
                  <td class="text-right" colspan="2">
                    {{
                      parseFloat($root.$data.erp.report.checkPaymentStatus(sales.total,
                          sales.payments))
                      - parseFloat(sales.previous_due) < 0 ? 'Due'
                          : parseFloat($root.$data.erp.report.checkPaymentStatus(sales.total, sales.payments))
                          - parseFloat(sales.previous_due) == 0 ? 'Due'
                              : parseFloat($root.$data.erp.report.checkPaymentStatus(sales.total, sales.payments))
                              - parseFloat(sales.previous_due) > 0 ? 'Adjustment Paid' : ''
                    }}
                  </td>
                  <td class="text-right strong">
                    {{
                      (parseFloat($root.$data.erp.report.checkPaymentStatus(sales.total, sales.payments))
                          - parseFloat(sales.previous_due)
                      ).toFixed(4)
                    }}
                  </td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-col>
          <v-col cols="12" md="12" v-if="returns.length>0">
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
                  <th>action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, index) in returns">
                  <td>
                    {{ index + 1 }}
                  </td>
                  <td>{{ item.product.code }}</td>
                  <td>{{ item.product.name }}</td>
                  <td>
                    {{ parseFloat(item.quantity).toFixed(4) }}
                  </td>
                  <td>{{ item.unit.key }}</td>
                  <td class="bg-warning-lt strong">{{ item.amount }}</td>
                  <td class="bg-warning-lt strong">
                    <v-btn x-small @click="showAmountEdit(item, index)">edit</v-btn>
                  </td>
                </tr>
                </tbody>
                <tfoot>
                <tr class="strong">
                  <td colspan="5" class="text-right">Total</td>
                  <td colspan="1" class="text-left bg-info-lt">
                    <router-link
                        :to="{name:'bank.transaction', params:{id:returns[0].transaction_id}}">
                      {{ totalRAmount }}
                    </router-link>
                  </td>
                </tr>
                </tfoot>
              </template>
            </v-simple-table>
          </v-col>
          <v-col md="6" cols="12" v-if="sales.note">
            Note: {{ sales.note }}
          </v-col>
          <v-col md="6" cols="12"></v-col>
        </v-row>
      </v-card-text>
      <!--    <sale-show-print-->
      <!--        :client_data="$data"-->
      <!--        style="visibility: collapse"-->
      <!--    >-->
      <!--    </sale-show-print>-->
    </v-card>
  </v-dialog>
</template>
<script>
// import SaleShowPrint from './sale-show-print.vue'
export default {
  components:{
    // SaleShowPrint
  },
  props: {
    value: {
      type: Number,
      default: null
    },
  },
  data() {
    return {
      payment_crud_method    : 'create',
      payment_crud_dialog    : false,
      payment_crud_model_id  : null,
      payment_crud_payment_id: null,
      forms                  : {
        price: 0,
        id   : null,
      },
      dialog                 : false,
      sales                  : {
        ref     : '',
        customer: {
          user: {}
        },
        payments: []
      },
      company                : {},
      returns                : [],
      totalRAmount           : 0,
    }
  },
  created() {
    this.getData()
  },
  watch  : {
    payment_crud_dialog(val) {
      if(!val) {
        this.resetPaymentComponent()
      }
    }
  },
  computed  : {
    product_total() {
      if(!_.isEmpty(this.sales) && !_.isEmpty(this.sales.products)) {
        return this.sales.products.reduce((acc, obj) => {
          return acc + obj.pivot.subtotal
        }, 0)
      }
      return 0
    },
    sub_total() {
      return this.product_total + this.sales.shipping_cost
    },
    discounted_amount() {
      return (this.sub_total * (this.sales.overall_discount / 100)).toFixed(4)
    },
    showModal: {
      get() {
        return !!this.value;
      },
      set(value) {
        if (!value) this.$emit('input', null)
      }
    }
  },
  methods: {
    editPayment(id) {
      this.payment_crud_payment_id = id
      this.payment_crud_method     = 'edit'
      this.payment_crud_dialog     = true
      this.payment_list_dialog     = false
    },
    createPayment() {
      this.resetPaymentComponent()
      this.payment_crud_model_id = this.sales.id
      this.payment_crud_dialog = true
    },
    resetPaymentComponent() {
      this.payment_crud_method = 'create'
      this.payment_crud_model_id = null
      this.payment_crud_payment_id = null
    },
    paymentSuccess(val) {
      if(val) {
        this.getData()
        this.payment_crud_dialog = false
      }
    },
    getData() {
      axios.get(api_base_url+'/sales/'+this.value)
          .then(res=>{
            let data = res.data
            this.sales        = data.sale
            this.company      = data.company
            this.returns      = data.returns
            this.totalRAmount = 0.0
            for (let i = 0; i < this.returns.length; i++) {
              this.totalRAmount += parseFloat(this.returns[i].amount)
            }
          })
    },
    showAmountEdit(item, index) {
      this.forms       = item
      this.forms.price = item.product.price
      this.dialog      = true
    },
    updateReturn() {
      axios.patch('/api/inventory/sale/' + this.$root.$route.params.id + '/return/' + this.forms.returnable_id, this.forms)
          .then(res => {
            if(res.status === 200) {
              Swal.fire({
                icon : res.data.type,
                text : res.data.message,
                timer: 3000
              })
                  .then((result) => {
                    this.getData()
                    this.dialog = false
                  })
            }
          })
          .catch(error => {
            Swal.fire(error.response.data.message)
          })
    }
  }
}

</script>