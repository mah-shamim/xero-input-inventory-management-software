<template>
  <div id="myPrint" v-if="!_.isEmpty(client_data)">
    <h3 class="text-center">{{ company.name }}</h3>
    <p class="text-center">Address1: {{ company.address1 }}</p>
    <p class="text-center" v-if="company.address2">Address2{{ company.address2 }}</p>
    <p class="text-center">Phone: <span v-for="ph in company.contact_phone">{{ ph }},</span></p>
    <hr>
    <div class="row">
      <div class="col-md-3">
        Ref: {{ client_data.ref }}<br>
        <template v-if="client_data.note">Note: {{ client_data.note }}</template>
      </div>
      <div class="col-md-3">
        Date: {{ client_data.income_date | removeTimeFromDate }}<br>
        Paid To:
        <template v-if="!_.isEmpty(client_data.userable)">{{ client_data.userable.name_id_type }}</template>
      </div>
      <div class="col-md-3">
        Warehouse:
        <template v-if="!_.isEmpty(client_data.warehouse)">{{ client_data.warehouse.name }}</template>
        <br>
        Account Name:
        <template v-if="!_.isEmpty(client_data.account)">{{ client_data.account.name }}</template>
      </div>
      <div class="col-md-3">
        Amount: {{ client_data.amount | tofix(2) }}<br>
        Created at: {{ client_data.created_at }}
      </div>
    </div>
    <br>
    <div class="row" v-if="!_.isEmpty(client_data.payments)">
      <table class="table table-bordered">
        <tr>
          <th class="text-center">date</th>
          <th class="text-center">paid</th>
          <th class="text-center">mood</th>
          <th class="text-center">bank</th>
        </tr>
        <tr v-for="item in client_data.payments">
          <td align="center">
            {{ item.date | removeTimeFromDate }}
          </td>
          <td align="center">
            {{ item.paid | tofix(2) }}
          </td>
          <td align="center">
            {{ item.transaction.payment_method }}
          </td>
          <td align="center">
            {{ item.transaction.bank.name }}
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name : "income-show-print",
  props: ['client_data'],
  data() {
    return {
      company: {}
    }
  },
  created() {
    this.company = auth.user.company
  }
}
</script>

<style scoped>

</style>