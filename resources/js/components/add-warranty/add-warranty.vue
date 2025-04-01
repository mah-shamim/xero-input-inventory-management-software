<template>
  <v-dialog width="800" v-model="showModal">
    <form @submit.prevent="postItem('forms')"
          data-vv-scope="forms">
      <v-card class="add-warranty">
        <v-card-title>
          {{ `${actionText}` }} Warranty
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="6">
              <search-product-dropdown :forms="forms"
                                       :required="'required'"
                                       dusk="search_product"
              ></search-product-dropdown>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field type="number"
                            name="quantity"
                            dusk="quantity"
                            label="Quantity"
                            v-validate="'required'"
                            data-vv-name="quantity"
                            v-model="forms.quantity"
                            :error-messages="errors.collect('forms.quantity')"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-select name="status"
                        dusk="status"
                        label="status"
                        :items="statuses"
                        data-vv-name="status"
                        v-model="forms.status"
                        v-validate="'required'"
                        :error-messages="errors.collect('forms.status')"
              ></v-select>
            </v-col>
            <v-col cols="12" md="6">
              <v-menu :close-on-content-click="false"
                      transition="scale-transition"
                      :nudge-right="40"
                      min-width="290px"
                      v-model="menu2"
                      offset-y
              >
                <template v-slot:activator="{ on }">
                  <v-text-field label="Picker without buttons"
                                v-model="forms.warranty_date"
                                v-on="on"
                                readonly
                                dusk="calender"
                                prepend-icon="mdi-calendar"
                  ></v-text-field>
                </template>
                <v-date-picker name="date"
                               @input="menu2 = false"
                               v-validate="'required'"
                               data-vv-name="warranty_date"
                               v-model="forms.warranty_date"
                               :error-messages="errors.collect('forms.warranty_date')"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12" md="6">
              <search-customer-dropdown :forms="forms"
                                        :required="'required'"
                                        dusk="search_customer"
              ></search-customer-dropdown>
            </v-col>
            <v-col cols="12" md="6">
              <v-textarea v-model="forms.note"
                          label="note"
              ></v-textarea>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-btn type="submit" dusk="submit" success>submit</v-btn>
        </v-card-actions>
      </v-card>
    </form>
  </v-dialog>
</template>

<script>
import _ from 'lodash'

export default {
  props: {
    id: {
      type: Number,
      default: null
    },
    value: {
      type: Boolean,
      default: false
    }
  },
  data: () => ({
    menu: false,
    modal: false,
    menu2: false,
    options: {
      itemsPerPage: 10,
    },
    forms: {
      id: null,
      warranty_date: new Date().toISOString().substr(0, 10),
      product: null,
      customer: null,
      product_id: null,
      customer_id: null
    },
    statuses: ['Receive from Customer',
      'Send to Supplier',
      'Receive from Supplier',
      'Delivered to Customer',
      'Damaged'],
  }),
  watch: {
    id: {
      immediate: true,
      handler(val) {
        if (val) this.editItem(val)
      },
    }
  },
  computed: {
    actionText() {
      if (this.id) {
        return 'Update '
      }

      return 'Create '
    },
    showModal: {
      get() {
        return this.value;
      },
      set(value) {
        if (!value) this.$emit('input', value)
      }
    }
  },
  methods: {
    postItem(scope) {
      let result = Promise.all([
        this.$validator.validate('forms.product'),
        this.$validator.validate('forms.customer')
      ])

      result.then(values => {
        if (values.includes(false)) {
          return null;
        }
      })

      this.$validator.validateAll(scope).then(result => {
        if (result) {
          this.forms.product_id = this.forms.product.id
          this.forms.customer_id = this.forms.customer.id
          !this.forms.id ? this.saveItem(scope) : this.updateItem(scope)
        }
      })
    },
    saveItem(scope) {
      axios.post('/api/inventory/warranty', this.forms)
          .then(res => {
            swal({
              type: res.data.type,
              timer: 2000,
              text: res.data.message,
              showCancelButton: false,
              showConfirmButton: false,
            }).catch(swal.noop)
          })
          .catch(error => {
            let err
            let errs = error.response.data.errors
            for (err in errs) {
              this.errors.add({
                'field': err,
                'msg': errs[err][0],
                scope: scope
              })
            }
          })
          .finally(() => {
            this.forms = {
              id: null,
              customer_id: null,
              product_id: null,
              note: null,
              warranty_date: new Date().toISOString().substr(0, 10),
              product: {},
              customer: {}
            }
            this.$emit('afterWarrantyAdd', true)
          })
    },
    editItem(val) {
      axios.get('/api/inventory/warranty/' + val + '/edit')
          .then(res => {
            this.forms = res.data.warranty
          })
    },
    updateItem(scope) {
      this.forms.customer_id = this.forms.customer.id
      this.forms.product_id = this.forms.product.id
      axios.patch('/api/inventory/warranty/' + this.forms.id, this.forms)
          .then(res => {
            swal({
              type: res.data.type,
              timer: 2000,
              text: res.data.message,
              showCancelButton: false,
              showConfirmButton: false,
            }).catch(swal.noop)
          })
          .catch(error => {
            let err
            let errs = error.response.data.errors
            for (err in errs) {
              this.errors.add({
                'field': err,
                'msg': errs[err][0],
                scope: scope
              })
            }
          })
          .finally(() => {
            this.forms = {
              id: null,
              customer_id: null,
              product_id: null,
              note: null,
              warranty_date: new Date().toISOString().substr(0, 10),
              product: {},
              customer: {}
            }
            this.$emit('update:value', false)
          })
    },
  }
};
</script>
