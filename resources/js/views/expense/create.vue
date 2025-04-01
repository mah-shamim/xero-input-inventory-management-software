<template>
  <form
      class="dense-form"
      @submit.prevent="postExpense('forms')"
      id="create_edit_expense_dialog"
      data-vv-scope="forms"
  >
    <input type="hidden" id="id" name="id" v-model="forms.id"/>
    <v-card
        max-width="full"
        :loading="loading"
    >
      <v-card-title>
        {{ forms.id ? 'Edit ' : 'Create ' }}Expense
        <v-spacer/>
      </v-card-title>
      <v-divider/>
      <v-card-text class="fill-height">
        <v-row>
          <v-col md="4" cols="12">
            <vuetify-datetime
                v-model="forms.expense_date"
                label="Expense Date"
                name="expense_date"
                required
            />
          </v-col>
          <v-col md="4" cols="12">
            <v-text-field
                dense
                dusk="bill_no"
                name="bill_no"
                label="bill no"
                data-vv-name="ref"
                v-model="forms.ref"
                v-validate="'required'"
                :error-messages="errors.collect('forms.ref')"
            />
          </v-col>
          <v-col md="4" cols="12">
            <v-text-field
                dense
                dusk="amount"
                type="number"
                name="amount"
                label="amount"
                data-vv-name="amount"
                v-model="forms.amount"
                v-validate="'required'"
                :error-messages="errors.collect('forms.amount')"
            >
            </v-text-field>
          </v-col>
          <v-col md="4" cols="12" id="accounts">
            <v-select
                dense
                label="Account"
                item-value="id"
                item-text="name"
                name="account"
                :items="accounts"
                v-model="forms.account_id"
                :error-messages="errors.collect('forms.account_id')"
                v-validate="'required'"
                data-vv-name="account"
            >
            </v-select>
          </v-col>
          <v-col md="4" cols="12" id="warehouse">
            <v-select
                dense
                item-value="id"
                item-text="name"
                label="warehouse"
                name="warehouse"
                :items="warehouses"
                v-model="forms.warehouse_id"
                :error-messages="errors.collect('forms.warehouse_id')"
                v-validate="'required'"
                data-vv-name="warehouse"
            >
            </v-select>
          </v-col>
          <v-col md="4" cols="12">
            <v-autocomplete
                dense
                cache-items
                return-object
                :items="users"
                item-text="name"
                dusk="search_user"
                name="userable_id"
                v-model="forms.userable_id"
                :search-input.sync="searchItems"
                :error-messages="errors.collect('forms.userable_id')"
                :label="forms.type==='debit'?'receive from':'pay to'"
            >
              <template v-slot:item="data">
                <template v-if="typeof data.item !== 'object'">
                  <v-list-item-content v-text="data.item"></v-list-item-content>
                </template>
                <template v-else>
                  <v-list-item-content>
                    <v-list-item-title v-html="data.item.name"></v-list-item-title>
                    <v-list-item-subtitle v-html="data.item.group"></v-list-item-subtitle>
                  </v-list-item-content>
                </template>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col md="4" cols="12">
            <v-text-field
                dense
                name="note"
                label="note"
                v-model="forms.note"
                :error-messages="errors.collect('forms.note')"
            >
            </v-text-field>
          </v-col>
        </v-row>
      </v-card-text>
      <v-divider/>
      <v-card-actions>
        <!--        payment popup will show-->
        <v-btn
            dusk="update"
            type="submit"
            color="success"
            :loading="loading"
            v-if="id"
        >
          Update
        </v-btn>
        <!--        payment popup will not show-->
        <v-btn
            type="submit"
            dusk="pay_now"
            color="primary"
            v-if="!id"
            :loading="loading"
            @click="pay_later=false"
        >
          Submit & Pay
        </v-btn>
        <v-btn
            type="submit"
            dusk="pay_later"
            color="primary"
            v-if="!id"
            :loading="loading"
            @click="pay_later=true"
        >
          Submit & Pay Later
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn text @click="$emit('input', false)">
          close
        </v-btn>
      </v-card-actions>
    </v-card>
  </form>
</template>
<script src="./create.js"></script>
