<template>
  <div id="myPrint" v-if="!_.isEmpty(client_data)">
    <h2 class="card-title">{{ auth.user.company.name }}</h2>
    <h5 class="card-subtitle">{{ auth.user.company.address1 }}</h5>
    <h5 class="text-center">
      {{ client_data.transaction.type === 'debit' ? 'Payment Receipt' : 'Bill Paid' }}
    </h5>
    <br>
    <div class="row">
      <div class="col-md-8">
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <div class="row">
              <div class="col-md-6">Payment Date:</div>
              <div class="col-md-6">{{ client_data.date | removeTimeFromDate }}</div>
            </div>
          </li>

          <li class="list-group-item">
            <div class="row">
              <div class="col-md-6">
                {{ client_data.transaction.type === 'debit' ? 'ref no' : 'bill no' }}
              </div>
              <div class="col-md-6">
                <template v-if="!_.isEmpty(client_data.paymentable)">
                    <span v-if="client_data.transaction.type === 'debit'">{{ client_data.paymentable.ref }}</span>
                    <span v-else>{{ client_data.paymentable.bill_no }}</span>
                </template>
                <template v-if="client_data.paymentable_type==='App\\Expense\\Expense'">
                  {{ client_data.paymentable.ref }}
                </template>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <div class="col-md-6">Payment Mood:</div>
              <div class="col-md-6">
                {{ client_data.payment_type | paymentMethod }}
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="row">
              <div class="col-md-6">Payment Through:</div>
              <div class="col-md-6">
                {{ client_data.transaction.bank.name }}
              </div>
            </div>
          </li>
        </ul>
        <br>
        <div class="pa-5">
          <p>
            {{ client_data.transaction.type === 'debit' ? 'Payment From' : 'Paid To' }}:
          </p>
          <div class="px-5">
            <p>{{ client_data.paymentable.userable.name }}</p>
            <p>Phone:{{ client_data.paymentable.userable.phone }}</p>
            <p>address:{{ client_data.paymentable.userable.address }}</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <p class="text-center">
          {{ client_data.transaction.type === 'debit' ? 'Amount Received' : 'Payment made' }}
        </p>
        <h2 class="text-center">
          {{ client_data.paid }}{{ auth.user.setting.settings.currency }}
        </h2>
      </div>
    </div>
    <br>
    <table class="table">
      <thead>
      <tr>
        <th>{{ client_data.transaction.type === 'debit' ? 'ref no' : 'bill no' }}</th>
        <th>{{ client_data.transaction.type === 'debit' ? 'Invoice Date' : 'Bill Date' }}</th>
        <th>{{ client_data.transaction.type === 'debit' ? 'Invoice Paid' : 'Bill Paid' }}</th>
        <th>Payment Amount</th>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td>
          {{ client_data.paymentable.ref }}
        </td>
        <td>
          {{ client_data.date | removeTimeFromDate }}
        </td>
        <td>{{ client_data.paymentable.amount }}{{ auth.user.setting.settings.currency }}</td>
        <td>{{ client_data.paid }}{{ auth.user.setting.settings.currency }}</td>
      </tr>
      </tbody>
    </table>
    <br>
    <p class="text-center">
      <template v-if="client_data.transaction.type === 'debit'">
        Payment History of Invoice No# {{ client_data.paymentable.ref }}
      </template>
      <template v-else>
        Payment History of Bill No# {{ client_data.paymentable.bill_no }}
      </template>
    </p>
    <table class="table">
      <thead>
      <tr>
        <th>date</th>
        <th>description</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(payment, index) in client_data.paymentable.payments" :key="index">
        <td>
          {{ payment.date | removeTimeFromDate }}
        </td>
        <td>
          Payment of amount <strong>{{ payment.paid }}{{ auth.user.setting.settings.currency }}</strong>
          made and applied for
          <strong>{{ client_data.paymentable.ref }}</strong>
          {{ client_data.transaction.type === 'debit' ? 'from' : 'to' }}
          <strong>{{ client_data.paymentable.userable.name }}</strong>
          by
          <strong v-if="!_.isEmpty(client_data.activities)">{{ client_data.activities[0].causer.name }}</strong>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>
<script>
export default {
  props: ['client_data'],
  data() {
    return {
      auth: auth,
    }
  }
}
</script>