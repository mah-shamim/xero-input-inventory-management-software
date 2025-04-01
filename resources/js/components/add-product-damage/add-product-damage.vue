<template>
  <v-dialog width="1000" v-model="showModal">
    <form @submit.prevent="postItem('forms')"
          data-vv-scope="forms">
      <v-card>
        <v-card-title v-text="`${actionText} Product Damage`"/>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="4">
              <warehouse-list-dropdown
                  :forms="forms"
                  :created="true"
                  :required="'required'"
                  dusk="search_warehouse"
              ></warehouse-list-dropdown>
            </v-col>
            <v-col cols="12" md="4">
              <v-autocomplete
                  item-value="id"
                  label="product"
                  item-text="name"
                  :items="products"
                  name="product_id"
                  dusk="search_product"
                  data-vv-as="product"
                  v-validate="'required'"
                  v-model="forms.product_id"
                  @change="getUnit($event)"
                  :disabled="products.length<=0"
                  :error-messages="errors.collect('forms.product_id')"
              ></v-autocomplete>
            </v-col>
            <v-col md="4" cols="12">
              <v-autocomplete
                  label="units"
                  name="unit_id"
                  dusk="search_unit"
                  :items="units"
                  item-text="key"
                  item-value="id"
                  data-vv-as="unit"
                  v-model="forms.unit_id"
                  v-validate="'required'"
                  :disabled="units.length<=0"
                  :error-messages="errors.collect('forms.unit_id')"
              ></v-autocomplete>
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  step="any"
                  type="number"
                  name="quantity"
                  dusk="quantity"
                  label="Quantity"
                  data-vv-name="quantity"
                  v-validate="'required'"
                  v-model="forms.quantity"
                  :error-messages="errors.collect('forms.quantity')"
              ></v-text-field>
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  step="any"
                  type="number"
                  name="sale_value"
                  dusk="sale_value"
                  label="Sale Value"
                  v-validate="'required'"
                  data-vv-as="sale value"
                  v-model="forms.sale_value"
                  :error-messages="errors.collect('forms.sale_value')"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions
                        v-if="forms.product_id && forms.unit_id && forms.warehouse_id && forms.quantity && forms.sale_value">
          <v-btn type="submit" dusk="submit" success>submit</v-btn>
        </v-card-actions>
      </v-card>
    </form>
  </v-dialog>

</template>

<script>
export default {
  name: 'add-product-damage',
  props:{
    id: {
      type: Number,
      default: null
    },
    value: {
      type: Boolean,
      default: false
    },
  },
  data: () => ({
    products: [],
    units: [],
    forms: {
      id: null,
      unit_id: null,
      quantity: null,
      sale_value: null,
      product_id: null,
      warehouse_id: null
    }
  }),
  watch: {
    id: {
      immediate: true,
      handler(val) {
        if (val) this.getItem(val)
      },
    },
    'forms.warehouse_id': function (val, oldVal) {
      if (!this.forms.id) {
        oldVal === null || val !== oldVal ? this.getProducts(val) : null
      }
    },
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
    getItem: _.debounce(function () {
      axios.get('/api/inventory/productdamages/' + this.id + '/edit')
          .then(res => {
            this.products = res.data.products
            this.units = res.data.units
            this.forms = res.data.productdamages
          })
    }),
    getProducts(val) {
      axios.get('/api/inventory/products/getProducts', {
        params: {
          filterWarehouse: true,
          warehouseId: val
        }
      })
          .then(res => {
            this.products = res.data.products
          })
    },
    getUnit(product_id) {
      console.log(product_id, this.products)
      if (this.products.length > 0 && this.forms.product_id) {
        let item = []
        let obj = {}
        item = this.products.filter(value => value.id === product_id)
        if (item.length > 0) {
          let obj = item[0]
          if (obj.id !== undefined) {
            if (obj.units !== undefined) {
              let units = obj.units
              if (units.length > 0) {
                this.units = units
              }
            }
          }
          // this.units = obj.hasOwnProperty('id') && obj.units.length > 0 ? item.units : []
        }
      }
    },
    postItem(scope) {
      let result = Promise.all([
        this.$validator.validate('forms.warehouse_id')
      ])

      result.then(values => {
        if (values.includes(false)) {
          return null;
        }
      })

      this.$validator.validateAll(scope).then(result => {
        if (result) {
          !this.forms.id ? this.saveItem(scope) : this.updateItem(scope)
        }
      })
    },
    saveItem(scope) {
      axios.post('/api/inventory/productdamages', this.forms)
          .then(res => {
            swal({
              type: res.data.type,
              timer: 2000,
              text: res.data.message,
              showCancelButton: false,
              showConfirmButton: false,
            })
                .then(r=>{
                  this.$emit('input', false)
                })
                .catch(swal.noop)
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
              unit_id: null,
              quantity: null,
              sale_value: null,
              product_id: null,
              warehouse_id: null,
            }
          })
    },
    updateItem(scope) {
      let url = '/api/inventory/productdamages/' + this.id
      axios.patch(url, this.forms)
          .then(res => {
            swal({
              type: res.data.type,
              timer: 2000,
              text: res.data.message,
              showCancelButton: false,
              showConfirmButton: false,
            })
                .then(r=>{
                  this.$emit('input', false)
                })
                .catch(swal.noop)
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
              unit_id: null,
              quantity: null,
              sale_value: null,
              product_id: null,
              warehouse_id: null,
            }
          })
    }
  }
};
</script>
