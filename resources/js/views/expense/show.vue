<template>
  <v-card outlined>
    <v-card-title>
      Ref #{{ item.ref }}
      <v-spacer></v-spacer>
<!--      <v-btn icon fab x-small @click="$htmlToPaper('myPrint')">-->
<!--        <v-icon>mdi-printer</v-icon>-->
<!--      </v-btn>-->
<!--      <v-btn icon fab x-small>-->
<!--        <v-icon>mdi-email</v-icon>-->
<!--      </v-btn>-->
<!--      <v-btn icon fab x-small>-->
<!--        <v-icon>mdi-pdf-box</v-icon>-->
<!--      </v-btn>-->
    </v-card-title>
    <v-card-text>
      <v-row>
        <v-col md="3" cols="12">Ref: {{ item.ref }}</v-col>
        <v-col md="3" cols="12">
          Expense Date: {{ item.expense_date | removeTimeFromDate }}
        </v-col>
        <v-col md="3" cols="12">
          Warehouse: {{ item.warehouse.name }}
        </v-col>
        <v-col md="3" cols="12">
          Amount: {{ item.amount | toFix(2) }}
        </v-col>
        <v-col md="3" cols="12" v-if="item.note">
          Note: {{ item.note }}
        </v-col>
        <v-col md="3" cols="12" v-if="!_.isEmpty(item.userable)">
          Paid To: {{ item.userable.name_id_type }}
        </v-col>
        <v-col md="3" cols="12" v-if="!_.isEmpty(item.account)">
          Account Name: {{ item.account.name }}
        </v-col>
        <v-col md="3" cols="12">
          Created at: {{ item.created_at }}
        </v-col>
        <v-col md="3" cols="12">
          updated at: {{ item.updated_at }}
        </v-col>
      </v-row>
      <v-divider></v-divider>
      <br>
      <p><strong>Payment detail</strong></p>
      <v-data-table
          :headers="paymentHeaders"
          :items="item.payments"
          class="elevation-0"
          hide-default-footer
      >
        <template v-slot:[`item.date`]="{item}">
          {{ item.date | removeTimeFromDate }}
        </template>
        <template v-slot:[`item.paid`]="{item}">
          {{ item.paid | toFix(2) }}
        </template>
        <template v-slot:[`item.mood`]="{item}">
          {{ item.payment_type | payment_by }}
        </template>
      </v-data-table>
    </v-card-text>
<!--    <expense-show-print-->
<!--        :client_data="item"-->
<!--        style="visibility: collapse"-->
<!--    />-->
  </v-card>
</template>
<script src="./show.js"></script>