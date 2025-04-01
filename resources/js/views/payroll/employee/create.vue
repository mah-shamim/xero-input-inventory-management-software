<template>
  <v-dialog v-model="showModal">
    <v-container fluid id="create_edit_employee">
      <v-form @submit.prevent="postEmployee('forms')"
              data-vv-scope="forms"
      >
        <input type="hidden" id="id" name="id" v-model="forms.id"/>
        <v-card
            max-width="full"
            :loading="loading"
        >
          <v-card-title>
            Employee {{ forms.id ? 'Edit' : 'Create' }}
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col md="3" cols="12">
                <v-text-field
                    required
                    persistent-hint
                    dusk="employee_id"
                    name="employee_id"
                    label="employee id"
                    v-validate="'required'"
                    hint="Type employee id"
                    v-model="forms.employee_id"
                    :error-messages="errors.collect('forms.employee_id')"
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12">
                <v-text-field
                    required
                    name="name"
                    dusk="name"
                    label="name"
                    persistent-hint
                    hint="Type name"
                    v-model="forms.name"
                    v-validate="'required'"
                    :error-messages="errors.collect('forms.name')"
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12" id="department">
                <v-select
                    required
                    item-value="id"
                    item-text="name"
                    dusk="department"
                    name="department"
                    label="department"
                    :items="departments"
                    v-model="forms.department_id"
                    :error-messages="errors.collect('department_id')"
                />
              </v-col>
              <v-col md="3" cols="12">
                <v-text-field
                    required
                    persistent-hint
                    dusk="designation"
                    name="designation"
                    label="designation"
                    v-validate="'required'"
                    hint="Type designation"
                    v-model="forms.designation"
                    :error-messages="errors.collect('forms.designation')"
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12" id="contract_type">
                <v-select
                    persistent-hint
                    hint="contact type"
                    name="contract_type"
                    dusk="contract_type"
                    label="Contact type"
                    v-validate="'required'"
                    v-model="forms.contract_type"
                    :items="$store.state.payroll_contact_types"
                    :error-messages="errors.collect('forms.contract_type')"

                ></v-select>
              </v-col>
              <v-col md="3" cols="12">
                <v-text-field
                    required
                    type="number"
                    dusk="salary"
                    name="salary"
                    label="salary"
                    persistent-hint
                    v-validate="'required'"
                    hint="Type salary"
                    v-model="forms.salary"
                    :error-messages="errors.collect('forms.salary')"
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12">
                <v-text-field
                    required
                    dusk="mobile"
                    name="mobile"
                    persistent-hint
                    label="mobile no"
                    v-validate="'required'"
                    hint="type mobile no"
                    v-model="forms.mobile"
                    :error-messages="errors.collect('forms.mobile')"
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12">
                <v-menu
                    offset-y
                    v-model="menu3"
                    :nudge-right="40"
                    min-width="290px"
                    transition="scale-transition"
                    :close-on-content-click="false"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                        readonly
                        v-on="on"
                        persistent-hint
                        name="join_date"
                        dusk="join_date"
                        label="join date"
                        prepend-icon="event"
                        v-validate="'required'"
                        hint="type join date"
                        v-model="forms.join_date"
                        :error-messages="errors.collect('forms.join_date')"
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="forms.join_date" @input="menu3 = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col md="3" cols="12">
                <v-menu
                    offset-y
                    v-model="menu2"
                    :nudge-right="40"
                    min-width="290px"
                    transition="scale-transition"
                    :close-on-content-click="false"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                        readonly
                        v-on="on"
                        persistent-hint
                        hint="birth day"
                        dusk="birth"
                        name="birth"
                        label="birth day"
                        prepend-icon="event"
                        v-validate="'required'"
                        v-model="forms.birth"
                        :error-messages="errors.collect('forms.birth')"
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="forms.birth" @input="menu2 = false"></v-date-picker>
                </v-menu>
              </v-col>
              <v-col md="3" cols="12">
                <v-text-field
                    required
                    name="nid"
                    dusk="nid"
                    persistent-hint
                    label="nid no"
                    hint="type nid no"
                    v-validate="'required'"
                    v-model="forms.nid"
                    :error-messages="errors.collect('forms.nid')"
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12">
                <v-text-field
                    required
                    persistent-hint
                    dusk="emergency"
                    name="emergency"
                    label="emergency no"
                    v-validate="'required'"
                    hint="type emergency no"
                    v-model="forms.emergency"
                    :error-messages="errors.collect('forms.emergency')"
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12">
                <v-text-field
                    required
                    dusk="address"
                    name="address"
                    label="address"
                    persistent-hint
                    hint="type address"
                    v-validate="'required'"
                    v-model="forms.address"
                    :error-messages="errors.collect('forms.address')"
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12">
                <v-textarea
                    required
                    name="note"
                    dusk="note"
                    label="note"
                    persistent-hint
                    hint="type note"
                    v-model="forms.note"
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
            >
              submit
            </v-btn>
            <v-btn @click="showModal=false">Close</v-btn>

            <!--                    <v-btn-->
            <!--                        type="submit"-->
            <!--                        color="success"-->
            <!--                        :loading="loading"-->
            <!--                    >-->
            <!--                        {{forms.id?'Update':'Submit'}}-->
            <!--                    </v-btn>-->
            <!--                    <v-btn-->
            <!--                        color="warning"-->
            <!--                        v-if="!forms.id"-->
            <!--                        @click="onCancel"-->
            <!--                    >-->
            <!--                        Reset-->
            <!--                    </v-btn>-->
          </v-card-actions>
        </v-card>
      </v-form>
    </v-container>
  </v-dialog>
</template>

<script src="./create.js"></script>
