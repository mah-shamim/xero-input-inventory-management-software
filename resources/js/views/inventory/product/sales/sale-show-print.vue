<template>
  <div id="myPrint">
    <div v-if="!_.isEmpty(client_data)">
      <h3 class="text-center">{{ company.name }}</h3>
      <p class="text-center">Address1: {{ company.address1 }}</p>
      <p class="text-center" v-if="company.address2">Address2{{ company.address2 }}</p>
      <p class="text-center">Phone: <span v-for="ph in company.contact_phone">{{ ph }},</span></p>
      <hr>
      <div class="row">
        <div class="col-md-6">
          <p class="text-subtitle-1">Customer Information</p>
          <p>name: {{ client_data.sale.customer.name_id }}</p>
          <p>email: {{ client_data.sale.customer.email }}</p>
          <p>phone: {{ client_data.sale.customer.phone }}</p>
          <p v-if="client_data.sale.customer.address">address: {{ client_data.sale.customer.address }}</p>
        </div>
        <div class="col-md-6">
          <p class="text-subtitle-1">Sale Information</p>
          <p>ref no: {{ client_data.sale.ref }}</p>
          <p>sale Date: {{ client_data.sale.sales_date | removeTimeFromDate }}</p>
          <p>created At: {{ client_data.sale.created_at }}</p>
          <!--        <barcode-->
          <!--            height="35px"-->
          <!--            font-size="10px"-->
          <!--            :value="`${client_data.ref}`"-->
          <!--        >-->
          <!--        </barcode>-->
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <h5>Product details:</h5>
          <table class="table table-bordered">
            <tr>
              <th class="text-center">No.</th>
              <th class="text-center">Code</th>
              <th class="text-center">Name(qty)</th>
              <th class="text-center">{{ $store.state.quantity_label }}</th>
              <th class="text-center">Unit Cost</th>
              <th class="text-center">Discount</th>
              <th class="text-center">Adjustment</th>
              <th class="text-center">Total</th>
              <th class="text-center">status</th>
            </tr>
            <tr
                v-for="(product, index) in client_data.sale.products"
                :key="index"
                style="border: 1px solid black !important;"
            >
              <td align="center">
                {{ index + 1 }}
              </td>
              <td align="center">{{ product.code }}</td>
              <td align="center">{{ product.name }}</td>
              <td align="center">
                {{ product.quantityStr }}({{ parseFloat(product.pivot.quantity).toFixed(2) }})
                <template v-if="product.manufacture_part_number">
                  <br>
                  <small>part number:</small>
                  <span
                      :key="'pn'+i"
                      v-for="(n, i) in $options.filters.sortPartNumber(
                            client_data.partnumbers,
                            product,
                            product.pivot.warehouse_id)"
                  >
                       <small>{{ n.part_number }}</small>,
                    </span>
                </template>
              </td>
              <td align="center">{{ product.pivot.price }}</td>
              <td align="center">{{ product.pivot.discount }}</td>
              <td align="center">{{ product.pivot.adjustment }}</td>
              <td align="center">{{ product.pivot.subtotal }}</td>
              <td align="center">{{ $root.productStatus(client_data.status) }}</td>
            </tr>
            <tr class="strong">
              <td colspan="7" align="right">
                Product Total
              </td>
              <td colspan="2">
                {{ product_total }}
              </td>
            </tr>
            <tr>
              <td colspan="7" align="right">{{ $root.shippingLabel }}</td>
              <td
                  colspan="2"
                  v-if="client_data.sale.shipping_cost"
              >
                {{ client_data.sale.shipping_cost }}
              </td>
              <td colspan="2" v-else>0</td>
            </tr>
            <tr>
              <td colspan="7" align="right">
                Sub Total
              </td>
              <td>
                {{ sub_total }}
              </td>
            </tr>
            <tr class="strong">
              <td colspan="7" align="right">
                overall discount
              </td>
              <td colspan="2" class="text-left bg-info" v-if="client_data.sale.overall_discount">
                {{ client_data.sale.overall_discount }}%({{ discounted_amount }})
              </td>
              <td colspan="2" class="text-left bg-info" v-else>
                0
              </td>
            </tr>
            <tr>
              <td colspan="7" align="right">Total</td>
              <td
                  colspan="2"
              >
                {{ client_data.sale.total }}
              </td>
            </tr>
          </table>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-12">
          <h5>Payment Detail</h5>
          <table class="table table-bordered">
            <thead>
            <tr>
              <th>Method</th>
              <th>Date</th>
              <th>Amount paid</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="payment in client_data.sale.payments">
              <td v-if="payment.transaction">
                {{ $root.paymentMethods(payment.payment_type) }}
              </td>
              <td v-else>
                {{ $root.paymentMethods(payment.payment_type) }}
              </td>
              <td>{{ payment.date | removeTimeFromDate }}</td>
              <td class="strong">{{ payment.paid }}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-md-12" v-if="client_data.returns.length>0">
          <h5>Product Return detail</h5>
          <table class="table table-bordered">
            <thead>
            <tr>
              <th>No.</th>
              <th>Code</th>
              <th>Name</th>
              <th>{{ $store.state.quantity_label }}</th>
              <th>Unit</th>
              <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, index) in client_data.returns">
              <td>
                {{ index + 1 }}
              </td>
              <td>{{ item.product.code }}</td>
              <td>{{ item.product.name }}</td>
              <td>{{ parseFloat(item.quantity).toFixed(4) }}</td>
              <td>{{ item.unit.key }}</td>
              <td class="bg-warning-lt strong">{{ item.amount }}</td>

            </tr>
            </tbody>
            <tfoot>
            <tr class="strong">
              <td colspan="5" class="text-right">Total</td>
              <td colspan="1" class="text-left bg-info-lt">
                {{ client_data.totalRAmount }}
              </td>
            </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ['client_data'],
  data() {
    return {
      company: {},
    }
  },
  mounted() {
    this.company = this.$store.state.settings.company_detail

  },
  computed: {
    product_total() {
      if (!_.isEmpty(this.client_data) && !_.isEmpty(this.client_data.sale.products)) {
        return this.client_data.sale.products.reduce((acc, obj) => {
          return acc + obj.pivot.subtotal
        }, 0)
      }
      return 0
    },
    sub_total() {
      return this.product_total + this.client_data.sale.shipping_cost
    },
    discounted_amount() {
      return (this.sub_total * (this.client_data.overall_discount / 100)).toFixed(4)
    }
  },
}
</script>