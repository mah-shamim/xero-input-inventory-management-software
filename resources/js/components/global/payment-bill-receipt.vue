<template>
  <div>
    <v-card>
      <v-card-title>
        {{ $root.company.name }}
        <v-spacer></v-spacer>
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
      </v-card-title>
      <v-card-subtitle>
        {{ $root.company.address1 }}
      </v-card-subtitle>
      <v-card-text>
        <h2 class="text-center">
          {{ item.transaction.type === 'debit' ? 'Payment Receipt' : 'Bill Paid' }}
        </h2>
        <v-row>
          <v-col md="8" cols="12">
            <v-simple-table>
              <template v-slot:default>
                <tbody>
                <tr>
                  <td>Payment Date</td>
                  <td>{{ item.date | removeTimeFromDate }}</td>
                </tr>
                <tr>
                  <td>
                    {{ item.transaction.type === 'debit' ? 'ref no' : 'bill no' }}
                  </td>
                  <td>
                  <span v-if="!_.isEmpty(item.paymentable)">
                    {{ item.paymentable.ref }}
                  </span>
                  </td>
                </tr>
                <tr>
                  <td>Payment Mood</td>
                  <td>{{ item.payment_type | payment_by }}</td>
                </tr>
                <tr>
                  <td>Payment Through</td>
                  <td>{{ item.transaction.bank.name }}</td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>
            <div class="pa-5">
              <p>{{ item.transaction.type === 'debit' ? 'Payment From' : 'Paid To' }}:</p>
              <div class="px-5">
                <p>{{ item.paymentable.userable.name }}</p>
                <p>Phone:{{ item.paymentable.userable.phone }}</p>
                <p>Address:{{ item.paymentable.userable.address }}</p>
              </div>
            </div>
          </v-col>
          <v-col md="4" cols="12">
            <div class="pa-16 success text-center text--white">
              <p class="white--text">
                {{ item.transaction.type === 'debit' ? 'Amount Received' : 'Payment made' }}
              </p>
              <h1 class="white--text">
                {{ item.paid }}
                 {{ $root.settings.currency }}
              </h1>
            </div>
          </v-col>
        </v-row>
        <v-divider></v-divider>
        <v-simple-table>
          <template v-slot:default>
            <thead>
            <tr>
              <th>{{ item.transaction.type === 'debit' ? 'ref no' : 'bill no' }}</th>
              <th>{{ item.transaction.type === 'debit' ? 'Invoice Date' : 'Bill Date' }}</th>
              <th>{{ item.transaction.type === 'debit' ? 'Invoice Paid' : 'Bill Paid' }}</th>
              <th>Payment Amount</th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>
                {{ item.paymentable.ref }}
              </td>
              <td>
                {{ item.date | removeTimeFromDate }}
              </td>
              <td>{{ item.paymentable.amount }}{{ $root.settings.currency }}</td>
              <td>{{ item.paid }}{{ $root.settings.currency }}</td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>

      </v-card-text>
    </v-card>
    <br>
    <v-card>
      <v-card-title>
        <template v-if="item.transaction.type === 'debit'">
          Payment History of Invoice No# {{ item.paymentable.ref }}
        </template>
        <template v-else>
          Payment History of Bill No# {{ item.paymentable.bill_no }}
        </template>
      </v-card-title>
      <v-card-text>
        <v-simple-table>
          <template v-slot:default>
            <thead>
            <tr>
              <th>date</th>
              <th>description</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(payment, index) in item.paymentable.payments" :key="index">
              <td>
                {{ payment.date | removeTimeFromDate }}
              </td>
              <td>
                Payment of amount <strong>
<!--                {{ payment.paid }}{{ auth.user.setting.settings.currency }}-->
              </strong>
                made and applied for
<!--                <strong>{{ item.paymentable.ref }}</strong>-->
<!--                {{ item.transaction.type === 'debit' ? 'from' : 'to' }}-->
<!--                <strong>{{ item.paymentable.userable.name }}</strong>-->
                by
                <strong v-if="!_.isEmpty(item.activities)">{{ item.activities[0].causer.name }}</strong>
              </td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card-text>
    </v-card>
<!--    <payment-bill-receipt-print-->
<!--        :client_data="item"-->
<!--        v-if="!_.isEmpty(item)"-->
<!--        style="visibility: collapse"-->
<!--    ></payment-bill-receipt-print>-->
  </div>
</template>

<script>
import PaymentBillReceiptPrint from './payment-bill-receipt-print.vue'

export default {
  components: {
    PaymentBillReceiptPrint
  },
  name      : "payment-bill-receipt",
  props     : {
    payment_id: {
      type: Number,
      default() {
        return null
      }
    }
  },
  data      : () => ({
    // auth: auth,
    item: {
      transaction: {
        bank: {}
      },
      paymentable: {
        payments: [],
        userable: {}
      }
    }
  }),
  created() {
    this.getData()
  },
  methods   : {
    getData() {
      axios
          .get('/api/payments/crud/' + this.payment_id)
          .then(res => {
            this.item = res.data
          })
    }
  }
}
</script>