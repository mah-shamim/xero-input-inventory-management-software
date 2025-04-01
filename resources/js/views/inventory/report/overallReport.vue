<template>
  <v-container fluid>
    <v-row>
      <v-col md="12" cols="12">
        <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="290px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
                v-model="date_between"
                label="Select Date Between"
                prepend-icon="event"
                readonly
                v-bind="attrs"
                v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker v-model="date_between" @input="date_between.length>1?menu = false:null" range></v-date-picker>
        </v-menu>
      </v-col>
    </v-row>
    <template v-if="date_between.length===2">
      <v-row>
        <v-col md="3" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[0]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Purchase Amount</v-card-title>
              <v-card-text>
                <p class="subtitle-1">{{ report.purchaseTotal.total_amount }}</p>
                <p>Purchase count: {{ report.purchaseTotal.total_count }}</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
        <v-col md="3" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[1]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Sales Amount</v-card-title>
              <v-card-text>
                <p class="subtitle-1">{{ getTotalSalesAmount(report.saleTotal) }}</p>
                <p>Sales count: {{ report.saleTotal.total_count }}</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
        <v-col md="3" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[2]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Expense Amount</v-card-title>
              <v-card-text>
                <p class="subtitle-1">
                  {{ (parseFloat(report.expenseTotal.total_amount) + parseFloat(report.salaryTotal.total_amount)).toFixed(4) }}</p>
                <p>Expense count:
                  {{ parseInt(report.expenseTotal.total_count) + parseInt(report.salaryTotal.total_count) }}</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
        <v-col md="3" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[3]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Income Amount</v-card-title>
              <v-card-text>
                <p class="subtitle-1">{{ report.incomeTotal.total_amount }}</p>
                <p>Income count: {{ report.incomeTotal.total_count }}</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
      </v-row>
      <v-row>
        <v-col md="4" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[4]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Gross Profit</v-card-title>
              <v-card-text>
                <p class="subtitle-1"> {{
                    parseFloat((report.saleTotal.total_amount - report.saleTotal.return_total)
                        - report.purchaseTotal.total_amount).toFixed(4)
                  }}</p>
                <p class="text--lighten-2">Equation: Sale - Purchase </p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
        <v-col md="4" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[5]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Net Profit</v-card-title>
              <v-card-text>
                <p class="subtitle-1">{{ netProfit }}</p>
                <p class="text--lighten-2">Equation: (sale+ income) - (purchase + expense)</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
        <v-col md="4" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[6]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Actual Profit</v-card-title>
              <v-card-text>
                <p class="subtitle-1">{{ actualProfit }}</p>
                <p class="subtitle-2">Equation: (sale+ income) - (purchase + expense)</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
      </v-row>
      <v-divider></v-divider>
      <br>
      <p class="subtitle-2">Transaction Type</p>
      <v-row>
        <v-col md="3" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[7]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Purchase</v-card-title>
              <v-card-text>
                <p class="subtitle-1">Cash: {{ report.purchaseTotal.cash_total }}</p>
                <p class="subtitle-2">Cheque:{{ report.purchaseTotal.cheque_total }}</p>
                <p class="subtitle-2">credit Card: {{ report.purchaseTotal.credit_total }}</p>
                <p class="subtitle-2">Purchase Due: {{ getPurchaseDue }}</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
        <v-col md="3" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[8]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.43), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Sale</v-card-title>
              <v-card-text>
                <p class="subtitle-1">Cash:
                  {{ parseFloat(report.saleTotal.cash_total) - parseFloat(report.saleTotal.return_total) }}</p>
                <p class="subtitle-2">Cheque: {{ report.saleTotal.cheque_total }}</p>
                <p class="subtitle-2">credit Card: {{ report.saleTotal.credit_total }}</p>
                <p class="subtitle-2">sale Due: {{ getSalesDue }}</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
        <v-col md="3" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[9]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Expense</v-card-title>
              <v-card-text>
                <p class="subtitle-1">Cash:
                  {{ (parseFloat(report.expenseTotal.cash_total) + parseFloat(report.salaryTotal.cash_total)).toFixed(4) }}</p>
                <p class="subtitle-2">
                  Cheque:{{ (parseFloat(report.expenseTotal.cheque_total) + parseFloat(report.salaryTotal.cheque_total)).toFixed(4) }}</p>
                <p class="subtitle-2">credit Card:
                  {{ (parseFloat(report.expenseTotal.credit_total) + parseFloat(report.salaryTotal.cheque_total)).toFixed(4) }}</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
        <v-col md="3" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[10]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Income</v-card-title>
              <v-card-text>
                <p class="subtitle-1">Cash: {{ report.incomeTotal.cash_total }}</p>
                <p class="subtitle-2">Cheque:{{ report.incomeTotal.cheque_total }}</p>
                <p class="subtitle-2">credit Card: {{ report.incomeTotal.credit_total }}</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
        <v-col md="3" cols="12">
          <v-card :loading="loading">
            <v-img
                :src="images[11]"
                class="white--text align-end"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="200px"
            >
              <v-card-title>Standing Balance</v-card-title>
              <v-card-text>
                <p class="subtitle-1">Cash: {{ parseFloat(cashTotal).toFixed(4) }}</p>
                <p class="subtitle-2">Cheque:{{ parseFloat(creditCardTotal).toFixed(4) }}</p>
                <p class="subtitle-2">credit Card: {{ parseFloat(chequeTotal).toFixed(4) }}</p>
              </v-card-text>
            </v-img>
          </v-card>
        </v-col>
      </v-row>
    </template>
    <template v-else>
      <v-alert
          outlined
          type="warning"
          prominent
          border="left"
      >
        Select Date Between
      </v-alert>
    </template>
  </v-container>
</template>

<script>
import {date_between} from "vee-validate/dist/rules.esm";

export default {
  data() {
    return {
      queryString: {},
      date_between: [],
      images: _.shuffle([
        require('../../../images/report-background-1.jpeg'),
        require('../../../images/report-background-2.jpeg'),
        require('../../../images/report-background-3.jpeg'),
        require('../../../images/report-background-4.jpeg'),
        require('../../../images/report-background-5.jpeg'),
        require('../../../images/report-background-6.jpeg'),
        require('../../../images/report-background-7.jpeg'),
        require('../../../images/report-background-8.jpeg'),
        require('../../../images/report-background-9.jpeg'),
        require('../../../images/report-background-10.jpeg'),
        require('../../../images/report-background-11.jpeg'),
        require('../../../images/report-background-12.jpeg')
      ]),
      menu: false,
      modal: false,
      report: {
        saleTotal: {
          total_amount: '',
          total_count: ''
        },
        purchaseTotal: {
          total_amount: '',
          total_count: ''
        },
        expenseTotal: {
          total_amount: '',
          total_count: ''
        },
        incomeTotal: {
          total_amount: '',
          total_count: ''
        },
        salaryTotal: {
          total_amount: '',
          total_count: ''
        },
        actualProfit: 0,
      }
    }
  },
  watch: {
    date_between(val) {
      if (val.length > 1) {
        if (_.size(val) > 0) {
          if (!val[0]) {
            delete this.queryString.date_between
          } else {
            this.queryString.date_between = []
            var fromDate = moment(val[1]).format("YYYY-MM-DD HH:mm:ss")
            if (moment(val[1]).format('HH:mm:ss') == '00:00:00') {
              fromDate = moment(val[1]).format("YYYY-MM-DD") + ' 23:59:59'
            }
            let date = [moment(val[0]).format("YYYY-MM-DD HH:mm:ss"), fromDate];
            this.queryString.date_between = date
          }
          this.getResults()
        }
      }
    }
  },
  computed: {
    cashTotal() {
      let purchase_cash = this.report.purchaseTotal.cash_total ? this.report.purchaseTotal.cash_total : 0
      let sale_cash = this.report.saleTotal.cash_total ? this.report.saleTotal.cash_total : 0
      let expense_cash = this.report.expenseTotal.cash_total ? this.report.expenseTotal.cash_total : 0
      let income_cash = this.report.incomeTotal.cash_total ? this.report.incomeTotal.cash_total : 0
      let salary_cash = this.report.salaryTotal.cash_total ? this.report.salaryTotal.cash_total : 0
      return (parseFloat(sale_cash) + parseFloat(income_cash)) - (parseFloat(purchase_cash) + parseFloat(expense_cash) + parseFloat(salary_cash))
    },
    creditCardTotal() {
      let purchase_cash = this.report.purchaseTotal.credit_total ? this.report.purchaseTotal.credit_total : 0
      let sale_cash = this.report.saleTotal.credit_total ? this.report.saleTotal.credit_total : 0
      let expense_cash = this.report.expenseTotal.credit_total ? this.report.expenseTotal.credit_total : 0
      let income_cash = this.report.incomeTotal.credit_total ? this.report.incomeTotal.credit_total : 0
      let salary_cash = this.report.salaryTotal.credit_total ? this.report.salaryTotal.credit_total : 0
      return (parseFloat(sale_cash) + parseFloat(income_cash)) - (parseFloat(purchase_cash) + parseFloat(expense_cash) + parseFloat(salary_cash))
    },
    chequeTotal() {
      let purchase_cash = this.report.purchaseTotal.cheque_total ? this.report.purchaseTotal.cheque_total : 0
      let sale_cash = this.report.saleTotal.cheque_total ? this.report.saleTotal.cheque_total : 0
      let expense_cash = this.report.expenseTotal.cheque_total ? this.report.expenseTotal.cheque_total : 0
      let income_cash = this.report.incomeTotal.cheque_total ? this.report.incomeTotal.cheque_total : 0
      let salary_cash = this.report.salaryTotal.cheque_total ? this.report.salaryTotal.cheque_total : 0
      return (parseFloat(sale_cash) + parseFloat(income_cash)) - (parseFloat(purchase_cash) + parseFloat(expense_cash) + parseFloat(salary_cash))
    },
    netProfit() {
      let netProfit = (parseFloat(this.report.saleTotal.total_amount) +
              parseFloat(this.report.incomeTotal.total_amount)) -
          (parseFloat(this.report.purchaseTotal.total_amount) +
              parseFloat(this.report.expenseTotal.total_amount) +
              parseFloat(this.report.salaryTotal.total_amount) +
              parseFloat(this.report.saleTotal.return_total));
      return parseFloat(netProfit).toFixed(4)
    },
    actualProfit() {
      let actualProfit = (parseFloat(this.report.actualProfit) +
              parseFloat(this.report.incomeTotal.total_amount)) -
          (parseFloat(this.report.expenseTotal.total_amount) +
              parseFloat(this.report.salaryTotal.total_amount));
      return parseFloat(actualProfit).toFixed(4)
    },
    getPurchaseDue() {
      let cash = this.report.purchaseTotal.cash_total ? this.report.purchaseTotal.cash_total : 0
      let cheque = this.report.purchaseTotal.cheque_total ? this.report.purchaseTotal.cheque_total : 0
      let credit = this.report.purchaseTotal.credit_total ? this.report.purchaseTotal.credit_total : 0
      let purchaseTotal = this.report.purchaseTotal.total_amount ? this.report.purchaseTotal.total_amount : 0
      let total = parseFloat(purchaseTotal) - (parseFloat(cash) + parseFloat(cheque) + parseFloat(credit))
      return total.toFixed(4)
    },
    getSalesDue() {
      let cash = this.report.saleTotal.cash_total ? this.report.saleTotal.cash_total : 0
      let cheque = this.report.saleTotal.cheque_total ? this.report.saleTotal.cheque_total : 0
      let credit = this.report.saleTotal.credit_total ? this.report.saleTotal.credit_total : 0
      let saleTotal = this.report.saleTotal.total_amount ? this.report.purchaseTotal.total_amount : 0
      return (parseFloat(saleTotal) - (parseFloat(cash) + parseFloat(cheque) + parseFloat(credit))).toFixed(4)
    }
  },
  created() {
    this.loading = true
    this.getResults()
    // this.images = _.shuffle(this.images)
  },
  methods: {
    getResults: _.debounce(function () {
      if(this.date_between.length !== 2) return false
      let url = '/api/report/overall?' + '&query=' + JSON.stringify(this.queryString)
      axios.get(url)
          .then(res => {
            this.report = res.data
            this.loading = false
          })
          .catch(err => {
            if (err.response.status === 422) {
              swal.fire({
                type: 'warning',
                text: 'select date between'
              })
            }
          })
    }, 800),

    getTotalSalesAmount(item) {
      return (item.total_amount ? item.total_amount : 0) - (item.return_total ? item.return_total : 0)
    }
  },
}
</script>

<style>

</style>
