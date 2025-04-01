<template>
  <div id="myPrint">
    <div class="text-center" v-if="!_.isEmpty(clientData.company_detail)">
      <h2 class="card-title">{{ clientData.company_detail.user.company.name }}</h2>
      <h5>
        Balance Sheet
      </h5>
      <div>
        Contact:
        <template v-if="!_.isEmpty(clientData.company_detail.user)"
                  v-for="(phone, index) in clientData.company_detail.user.company.contact_phone">
          {{ phone }}
        </template>
      </div>
      <p>Address: {{ clientData.company_detail.user.company.address1 }}</p>
    </div>
    <table class="table" v-if="!_.isEmpty(clientData.response_data)">
      <thead>
      <tr>
        <th>Particular</th>
        <th>Amount</th>
        <th>Amount</th>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td colspan="3" class="font-weight-black">
          Assets
        </td>
      </tr>
      <!--Fixed Assets-->
      <tr>
        <td>
          <span class="ml-2 font-weight-black">1. Fixed Assets</span>
        </td>
        <td colspan="2">{{ clientData.response_data.fixed_asset }}</td>
      </tr>
      <tr>
        <td class="text-right">
          <strong>Total Fixed Assets</strong>
        </td>
        <td colspan="2" class="text-center">
          <strong>{{ clientData.response_data.fixed_asset }}</strong>
        </td>
      </tr>

      <!--Current Assets-->
      <tr>
        <td colspan="3">
          <span class="ml-2 font-weight-black">2. Current Assets</span>
        </td>
      </tr>
      <tr>
        <td>
          <span class="ml-6">Cash in Hand</span>
        </td>
        <td colspan="2">{{ clientData.response_data.cash_in_hand }}</td>
      </tr>
      <tr>
        <td>
          <span class="ml-6">Cash in Bank</span>
        </td>
        <td colspan="2">{{ clientData.response_data.cash_in_bank }}</td>
      </tr>
      <tr>
        <td>
          <span class="ml-6">Merchandise Inventory</span>
        </td>
        <td colspan="2">{{ clientData.response_data.merchandise_inventory }}</td>
      </tr>
      <tr>
        <td>
          <span class="ml-6">Account Receivable</span>
        </td>
        <td colspan="2">{{ clientData.response_data.account_receivable }}</td>
      </tr>
      <tr>
        <td class="text-right">
          <strong>Total Current Asset</strong>
        </td>
        <td colspan="2" class="text-center">
          <strong>{{ clientData.response_data.total_current_asset }}</strong>
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Total Assets</strong>
        </td>
        <td colspan="2" class="text-center">
          <strong>{{ clientData.response_data.total_assets }}</strong>
        </td>
      </tr>


      <tr>
        <td colspan="3" class="font-weight-black">Liabilities and Owners Equity</td>
      </tr>
      <tr>
        <td colspan="3" class="font-weight-black">
          <span class="ml-2">3. Current Liabilities</span>
        </td>
      </tr>
      <tr>
        <td>
          <span class="ml-6">Accounts Payable</span>
        </td>
        <td colspan="2">{{ clientData.response_data.account_payable }}</td>
      </tr>
      <tr>
        <td class="font-weight-black" colspan="3">
          <span class="ml-2">4. Owner's Equity</span>
        </td>
      </tr>
      <tr>
        <td>
              <span class="ml-6">
                Owner's Investment
              </span>
        </td>
        <td colspan="2">
          {{
            clientData.response_data.owner_investment
          }}
        </td>

      </tr>
      <tr>
        <td>
              <span class="ml-6">
                Retained Earnings
              </span>
        </td>
        <td colspan="2">
          {{
            clientData.response_data.profit
          }}
        </td>
      </tr>

      <tr>
        <td class="text-right">
          <strong>Total Liability and Owner Equity</strong>
        </td>
        <td colspan="2" class="text-center">
          <strong>
            {{
              clientData.response_data.total_liabilities
              +
              clientData.response_data.total_owner_equity
            }}
          </strong>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "balance-sheet-print",
  props:['clientData']
}
</script>

<style scoped>

</style>