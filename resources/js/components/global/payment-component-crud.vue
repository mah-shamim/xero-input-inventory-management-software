<template>
  <v-dialog v-model="showModal" width="800">
    <v-card outlined class="elevation-0" id="global_payment_crud">
      <v-form @submit.prevent="submitForm()" class="dense-form">
        <v-card-title class="text-capitalize">
          <template v-if="model==='sale' || model==='income'">
            {{ payment_id ? 'Update' : 'Make' }} Payment
          </template>
          <template v-if="model==='expense' || model === 'purchase'">
            {{ payment_id ? 'Update' : 'Pay' }} Bill
          </template>
          <v-spacer/>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col md="6" cols="12">
              <vuetify-datetime
                  v-model="forms.date"
                  label="Purchase Date"
                  required
              />
            </v-col>
            <v-col md="6" cols="12">
              <v-select
                  dense
                  label="type"
                  item-value="id"
                  item-text="name"
                  name="payment_type"
                  v-model="forms.payment_type"
                  :items="$store.state.paymentMethods"
                  :error-messages="forms.errors.payment_type"
              />
            </v-col>
            <v-col md="6" cols="12">
              <v-select
                  dense
                  :items="banks"
                  item-value="id"
                  item-text="name"
                  label="select bank"
                  v-model="forms.bank_id"
                  :error-messages="forms.errors.bank_id"
              >
                <template v-slot:item="data">
                  <template v-if="typeof data.item !== 'object'">
                    <v-list-item-content
                        class="small"
                        v-text="data.item"
                    />
                  </template>
                  <template v-else>
                    <v-list-item-content class="small">
                      <span v-html="data.item.name"/>
                    </v-list-item-content>
                    <v-spacer/>
                    <v-list-item-content>
                      <v-list-item-subtitle class="small">
                        <i class="mdi mdi-currency-usd"></i>
                        {{ data.item.running_balance }}
                      </v-list-item-subtitle>
                    </v-list-item-content>
                  </template>
                </template>
              </v-select>
            </v-col>
            <v-col md="6" cols="12">
              <v-autocomplete
                  dense
                  :items="ids"
                  return-object
                  item-value="id"
                  label="bill no"
                  item-text="bill_no"
                  v-if="model==='purchase'"
                  v-model="forms.model_object"
                  :search-input.sync="searchValue"
                  :multiple="multipleIds.length>0"
                  :error-messages="forms.errors.model_object"
                  :disabled="(method==='edit' || !!model_id) ||  multipleIds.length>0"
              />
              <v-autocomplete
                  dense
                  :items="ids"
                  return-object
                  item-value="id"
                  label="bill no"
                  item-text="ref"
                  v-if="model==='expense'"
                  v-model="forms.model_object"
                  :search-input.sync="searchValue"
                  :multiple="multipleIds.length>0"
                  :error-messages="forms.errors.model_object"
                  :disabled="(method==='edit' || !!model_id) ||  multipleIds.length>0"
              />
              <v-autocomplete
                  dense
                  :items="ids"
                  return-object
                  item-value="id"
                  label="ref no"
                  item-text="ref"
                  v-if="model==='income'"
                  v-model="forms.model_object"
                  :search-input.sync="searchValue"
                  :multiple="multipleIds.length>0"
                  :error-messages="forms.errors.model_object"
                  :disabled="(method==='edit' || !!model_id) ||  multipleIds.length>0"
              />
              <v-autocomplete
                  dense
                  label="ref"
                  :items="ids"
                  return-object
                  item-value="id"
                  item-text="ref"
                  v-if="model==='sale'"
                  v-model="forms.model_object"
                  :search-input.sync="searchValue"
                  :disabled="method==='edit' || !!model_id"
                  :error-messages="forms.errors.model_object"
              />
            </v-col>
            <v-col md="6" cols="12" v-if="forms.payment_type == 2 || forms.payment_type == 3">
              <v-text-field
                  dense
                  label="transaction number"
                  v-model="forms.transaction_number"
              />
            </v-col>
            <v-col md="6" cols="12" v-if="forms.payment_type == 3">
              <v-text-field
                  dense
                  name="cheque_number"
                  label="cheque number"
                  v-model="forms.cheque_number"
                  :error-messages="forms.errors.cheque_number"
              />
            </v-col>
            <v-col md="6" cols="12">
              <v-text-field
                  dense
                  name="paid"
                  type="number"
                  label="amount"
                  v-model="forms.paid"
                  dusk="payment_crud_paid"
                  :error-messages="forms.errors.paid"
              />
            </v-col>
            <v-col md="6" cols="12">
              <v-text-field
                  dense
                  name="note"
                  label="note"
                  v-model="forms.note"
                  :error-messages="forms.errors.note"
              />
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-btn
              text
              outlined
              dusk="submit"
              type="submit"
              color="success"
              :loading="loading"
              :disabled="loading"
          >
            submit
          </v-btn>
          <v-btn text @click="$emit('input', false)">
            Close
          </v-btn>
        </v-card-actions>
      </v-form>
    </v-card>
  </v-dialog>
</template>

<script src="./payment-component-crud.js"></script>