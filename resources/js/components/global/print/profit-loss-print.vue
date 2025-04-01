<template>
  <div id="myPrint" >
    <div class="text-center" v-if="!_.isEmpty(clientData.company_detail)">
      <h2 class="card-title">{{ clientData.company_detail.user.company.name }}</h2>
      <h5>
        Profit & Loss Statement
      </h5>
      <div>
        Contact:
        <template v-if="!_.isEmpty(clientData.company_detail.user)" v-for="(phone, index) in clientData.company_detail.user.company.contact_phone">
          {{ phone }}
        </template>
      </div>
      <p>Address: {{ clientData.company_detail.user.company.address1 }}</p>
    </div>
    <div>
      <table class="table" v-if="!_.isEmpty(clientData.response_data)">
        <thead>
        <tr>
          <th>Particular</th>
          <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <td><strong>Income</strong></td>
          <td></td>
        </tr>
        <tr>
          <td>
            <span class="ml-3">Sale Profit(+)</span>
          </td>
          <td>{{ clientData.response_data.sale_profit }}</td>
        </tr>
        <tr>
          <td>
            <span class="ml-3">Sale Return(-)</span>
          </td>
          <td>{{ clientData.response_data.sale_returns }}</td>
        </tr>
        <tr v-for="(key, income) in clientData.response_data.income_accounts">
          <td>
            <span class="ml-3">{{ income }}</span>
          </td>
          <td>{{ key }}</td>
        </tr>
        <tr>
          <td class="text-right">
            Total Income
          </td>
          <td>{{ clientData.response_data.summation.total_income }}</td>
        </tr>
        <tr>
          <td class="text-right">Gross Profit</td>
          <td>{{ clientData.response_data.summation.gross_profit }}</td>
        </tr>
        <tr>
          <td>
            <strong>Expenses</strong>
          </td>
          <td></td>
        </tr>
        <tr>
          <td><span class="ml-3">Payroll</span></td>
          <td>{{ clientData.response_data.salaries }}</td>
        </tr>
        <tr v-for="(key, expense) in clientData.response_data.expense_accounts">
          <td>
            <span class="ml-3">{{ expense }}</span>
          </td>
          <td>{{ key }}</td>
        </tr>
        <tr>
          <td class="text-right">
            Total Expense
          </td>
          <td>
            {{ clientData.response_data.summation.total_expense }}
          </td>
        </tr>
        <tr>
          <td class="text-right">Net Profit</td>
          <td>{{ clientData.response_data.summation.net_profit }}</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "profit-loss-print",
  props: ['clientData']
}
</script>

<style scoped>

</style>