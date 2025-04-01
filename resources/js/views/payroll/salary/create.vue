<template>
  <v-dialog v-model="showModal">
    <v-card id="salary_create_edit">
      <v-form
          data-vv-scope="forms"
          @submit.prevent="postSalary('forms')"
      >
        <input type="hidden" id="id" name="id" v-model="forms.id"/>
        <v-card
            max-width="full"
            :loading="loading"
        >
          <v-card-title>
            Salary Create
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col md="4" cols="12">
                <v-menu
                    offset-y
                    ref="menu"
                    min-width="290px"
                    transition="scale-transition"
                    :close-on-content-click="false"
                    :return-value.sync="forms.salary_date"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                        readonly
                        v-on="on"
                        name="salary_date"
                        prepend-icon="event"
                        label="Date of salary:"
                        v-validate="'required'"
                        v-model="forms.salary_date"
                        :value="computedSalaryDate"
                        :error-messages="errors.collect('forms.salary_date')"
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="forms.salary_date" @input="menu=false">
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col md="4" cols="12">
                <v-autocomplete
                    tabindex="2"
                    item-value="id"
                    item-text="name"
                    :items="employees"
                    label="Employees"
                    dusk="employee_id"
                    name="employee_id"
                    @input="getSalary()"
                    v-validate="'required'"
                    v-model="forms.employee_id"
                    :error-messages="errors.collect('forms.employee_id')"
                ></v-autocomplete>
              </v-col>
              <v-col md="4" cols="12">
                <v-menu
                    offset-y
                    ref="menu"
                    v-model="menu"
                    max-width="290px"
                    min-width="290px"
                    transition="scale-transition"
                    :close-on-content-click="false"
                    :return-value.sync="forms.salary_month"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-on="on"
                        persistent-hint
                        name="salary_month"
                        label="salary month"
                        prepend-icon="event"
                        v-validate="'required'"
                        v-model="forms.salary_month"
                        :error-messages="errors.collect('forms.salary_month')"
                    >

                    </v-text-field>
                  </template>
                  <v-date-picker
                      no-title
                      scrollable
                      type="month"
                      v-model="date"
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="menu = false">Cancel</v-btn>
                    <v-btn text color="primary" @click="$refs.menu.save(date)">OK</v-btn>
                  </v-date-picker>
                </v-menu>

                <v-text-field
                    step="any"
                    type="number"
                    dusk="amount"
                    name="amount"
                    label="amount"
                    persistent-hint
                    hint="type amount"
                    v-model="forms.amount"
                    v-validate="'required'"
                    :error-messages="errors.collect('forms.amount')"
                ></v-text-field>

                <v-text-field
                    step="any"
                    type="number"
                    persistent-hint
                    dusk="festival_bonus"
                    name="festival_bonus"
                    label="festival bonus"
                    v-validate="'required'"
                    hint="type festival bonus"
                    v-model="forms.festival_bonus"
                    :error-messages="errors.collect('forms.festival_bonus')"
                ></v-text-field>

                <v-text-field
                    step="any"
                    type="number"
                    persistent-hint
                    dusk="other_bonus"
                    name="other_bonus"
                    label="other bonus"
                    hint="type other bonus"
                    v-validate="'required'"
                    v-model="forms.other_bonus"
                    :error-messages="errors.collect('forms.other_bonus')"
                ></v-text-field>

                <v-text-field
                    type="number"
                    persistent-hint
                    dusk="deduction"
                    name="deduction"
                    label="deduction"
                    hint="type deduction"
                    v-validate="'required'"
                    v-model="forms.deduction"
                    :error-messages="errors.collect('forms.deduction')"
                ></v-text-field>

                <div id="payment_method">
                  <v-select
                      item-value="id"
                      item-text="name"
                      dusk="payment_method"
                      name="payment_method"
                      label="Payment Method"
                      v-validate="'required'"
                      v-model="forms.payment_method"
                      :items="$store.state.paymentMethods"
                      :error-messages="errors.collect('forms.payment_method')"
                  ></v-select>
                </div>

                <h3>Total Amount : {{ totalAmount }} </h3>

              </v-col>
              <v-col md="12" cols="12">
                <v-textarea
                    name="note"
                    label="Note"
                    v-model="forms.note"
                    placeholder="Write salary note here"
                    :error-messages="errors.collect('forms.note')"
                ></v-textarea>
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions>
            <v-btn
                dusk="submit"
                type="submit"
                :loading="loading"
                :disabled="loading"
                @click="postSalary('forms')"
            >
              submit
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-card>
  </v-dialog>
</template>
<script src="./create.js"></script>

