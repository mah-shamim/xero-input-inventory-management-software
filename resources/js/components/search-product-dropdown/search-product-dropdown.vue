<template>
  <v-autocomplete
      outlined
      name="product"
      return-object
      item-value="id"
      color="success"
      label="Product"
      item-text="name"
      :items="products"
      data-vv-name="product"
      :loading="productInput"
      v-model="forms.product"
      v-validate="`${required}`"
      placeholder="Search Product"
      :search-input.sync="searchProduct"
      :filter="$options.filters.customFilter"
      :error-messages="errors.collect('forms.product')"
  >
    <template v-slot:item="data">
      <template v-if="typeof data.item !== 'object'">
        <v-list-item-content v-text="data.item"
        ></v-list-item-content>
      </template>
      <template v-else>
        <v-list-item-content>
          <span v-html="data.item.name"></span>
        </v-list-item-content>
        <v-list-item-content>
          <v-list-item-subtitle v-html="data.item.code"
          ></v-list-item-subtitle>
        </v-list-item-content>
      </template>
    </template>
  </v-autocomplete>
</template>

<script>
export default {
  inject: ['$validator'],
  props: ['forms', 'required'],
  data: () => ({
    products: [],
    productInput: false,
    entries: [],
    searchProduct: null,
  }),
  watch: {
    forms: {
      handler(val) {
        if (val) {
          if (val.hasOwnProperty('company_id') && !this.products.length) {
            this.products.push(val.product)
          }
        }
      },
      immediate: true
    },
    searchProduct: _.debounce(function (val) {
      this.getProducts(val)
    }, 800)
  },
  methods: {
    getProducts(query) {
      this.productInput = true
      axios.get('/api/inventory/products/getProducts', {
        params: {
          val: query,
          isPurchase: true
        }
      })
          .then(response => {
            this.products = response.data.products
            this.productInput = false
          })
          .catch(error => {
            alert(error.message)
          })
          .finally(() => (this.productInput = false))
    },

    customFilter(item, queryText, itemText) {
      const textOne = item.name.toString().toLowerCase()
      const textTwo = item.code.toString().toLowerCase()
      const searchText = queryText.toString().toLowerCase()
      return textOne.indexOf(searchText) > -1 || textTwo.indexOf(searchText) > -1
    },
  }
};
</script>
