<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Expense Report
        <v-spacer></v-spacer>
        <v-btn icon @click="$htmlToPaper('list_print_bootstrap')">
          <v-icon>mdi-printer</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col>
            <v-menu
                offset-y
                v-model="menu"
                min-width="290px"
                :nudge-right="40"
                transition="scale-transition"
                :close-on-content-click="false"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    dense
                    readonly
                    clearable
                    v-on="on"
                    @click.stop
                    hide-details
                    v-bind="attrs"
                    label="expense date"
                    v-model="options.expense_date"
                >
                </v-text-field>
              </template>
              <v-date-picker
                  range
                  v-model="options.expense_date"
                  @input="options.expense_date.length>1?menu = false:null"
              >
              </v-date-picker>
            </v-menu>
          </v-col>
          <v-col>
            <v-select
                dense
                clearable
                @click.stop
                hide-details
                item-value="id"
                item-text="name"
                :items="accounts"
                label="select account"
                v-model="options.account_id"
            />
          </v-col>
          <v-col>
            <v-text-field
                dense
                clearable
                @click.stop
                hide-details
                label="amount"
                v-model="options.amount"
            />
          </v-col>
          <v-col>
            <v-text-field
                dense
                clearable
                hide-details
                label="warehouse"
                v-model="options.warehouse"
            />
          </v-col>
          <v-col>
            <v-text-field
                dense
                clearable
                @click.stop
                hide-details
                label="bill no"
                v-model="options.ref"
            />
          </v-col>
        </v-row>
        <v-row>
          <v-col md="12" cols="12" id="list_print_div">
            <v-data-table
                :headers="headers"
                :loading="loading"
                :items="items.data"
                class="elevation-0"
                :options.sync="options"
                :server-items-length="items.total"
                loading-text="Loading... Please wait"
                :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
            >
              <template v-slot:footer.page-text>
                total amount: {{ total_amount|toFix(2) }},
                Total Paid: {{ total_paid|toFix(2) }},
                Quantity: {{ items.total }}
              </template>
              <template v-slot:[`item.paid_total`]="{item}">
                {{ item.paid_total | toFix(2) }}
              </template>
              <template v-slot:[`item.due`]="{item}">
                {{ item.due | toFix(2) }}
              </template>
              <template v-slot:[`item.expense_date`]="{item}">
                {{ item.expense_date | removeTimeFromDate }}
              </template>
              <template v-slot:[`item.amount`]="{item}">
                {{ item.amount | toFix(2) }}
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-card-text>
      <list-print-bootstrap
          :title="'Expense Report'"
          :columns="headers"
          :rows="items.data"
          style="visibility: collapse"
          :date_fields="['expense_date','created_at']"
      />
    </v-card>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      accounts: [],
      menu: false,
      total_amount: 0,
      total_paid: 0,
      loading: false,
      items: {},
      queryString: {},
      options: {
        itemsPerPage: this.$store.state.itemsPerPage
      },
      headers: [
        {text: 'bill date', value: 'expense_date', sortable: true},
        {text: 'bill no', value: 'ref', sortable: true},
        {text: 'warehouse', value: 'warehouse.name', sortable: false},
        {text: 'account', value: 'account.name', sortable: false},
        // {text: 'name-id-type', value: 'transaction.userable.name_id_type', sortable: false},
        {text: 'amount', value: 'amount', sortable: true},
        {text: 'paid', value: 'paid_total', sortable: true},
        {text: 'due', value: 'due', sortable: true},
        {text: 'note', value: 'note', sortable: false},
        {text: 'create at', value: 'created_at', sortable: true},
      ],
    }
  },
  watch: {
    options: {
      deep: true,
      handler() {
        this.loading = true
        this.getResults()
      }
    }
  },
  methods: {
    getResults() {
      axios
          .get('/api/report/expenses', {params: this.options})
          .then(res => {
            this.items = res.data.expenses
            this.total_amount = res.data.total_amount
            this.total_paid = res.data.paid_total
            this.accounts = res.data.accounts
          })
          .catch(error => {
            this.loading = false
          })
          .finally(() => {
            this.loading = false
          })
    }
  }
}
</script>
<style scoped>
::v-deep th {
  padding: 0 3px !important;
}
</style>