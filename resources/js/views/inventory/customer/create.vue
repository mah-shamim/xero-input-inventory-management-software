<template>
  <v-dialog width="800" v-model="showModal">
    <form @submit.prevent="postItem('forms')"  id="create_customer_dialog" data-vv-scope="forms">
      <v-card>
        <v-card-title>
          Customer
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="4" md="4">
              <v-text-field
                  label="name"
                  hint="bob, jack, karim"
                  persistent-hint
                  name="name"
                  dusk="name"
                  v-model="forms.name"
                  v-validate="'required'"
                  :error-messages="errors.collect('forms.name')"
                  data-vv-name="name"
                  required
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="4" md="4">
              <v-text-field
                  label="email"
                  persistent-hint
                  name="email"
                  dusk="email"
                  v-model="forms.email"
                  data-vv-name="email"
                  v-validate="'email'"
                  type="email"
                  :error-messages="errors.collect('forms.email')"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="4" md="4">
              <v-text-field
                  label="code"
                  persistent-hint
                  name="code"
                  dusk="code"
                  v-model="forms.code"
                  data-vv-name="code"
                  v-validate="'required'"
                  type="code"
                  :error-messages="errors.collect('forms.code')"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="4" md="4">
              <v-text-field
                  label="phone"
                  persistent-hint
                  name="phone"
                  dusk="phone"
                  v-model="forms.phone"
                  data-vv-name="phone"
                  v-validate="'required'"
                  :error-messages="errors.collect('forms.phone')"
                  required
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="8" md="8">
              <v-text-field
                  type="text"
                  label="address"
                  persistent-hint
                  name="address"
                  dusk="address"
                  v-model="forms.address"
                  data-vv-name="address"
                  :error-messages="errors.collect('forms.address')"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" md="6">
              <v-textarea
                  label="detail"
                  persistent-hint
                  name="detail"
                  dusk="detail"
                  v-model="forms.detail"
                  data-vv-name="detail"
                  :error-messages="errors.collect('forms.detail')"
              ></v-textarea>
            </v-col>
            <v-col cols="12" sm="4" md="4">
              <v-checkbox
                  v-model="forms.is_default"
                  label="is default"
              ></v-checkbox>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions id="customer_actions">
          <v-btn
              color="success darken-1"
              text
              type="submit"
              dusk="submit"
              success
          >
            Save
          </v-btn>
          <v-btn
              color="blue darken-1"
              text
              @click="$emit('input', false)"
          >
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </form>
  </v-dialog>
</template>
<script src="./create.js"></script>