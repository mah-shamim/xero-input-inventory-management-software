<template>
  <v-card>
    <v-card-title>{{ product.name }}-{{ product.code }}</v-card-title>
    <v-divider></v-divider>
    <v-card-text>
      <v-simple-table>
        <template v-slot:default>
          <thead>
          <tr>
            <th>Warehouse</th>
            <th>Location</th>
          </tr>
          </thead>
          <tbody>
          <template v-for="warehouse in product.warehouses">
            <tr>
              <td>{{warehouse.name}}</td>
              <td>
                <p v-for="(location, key) in warehouse.pivot.location" :key="key">
                  location Id: {{key}}, Quantity: {{location.quantity}}, weight: {{location.weight}}
                </p>
              </td>
            </tr>
          </template>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
  </v-card>
</template>
<script>
import _ from "lodash";

export default {
  name   : "a-product-locations-list",
  props  : {
    ProductId: {
      type    : Number,
      default : null,
      required: true
    }
  },
  data() {
    return {
      product: {
        name      : '',
        code      : '',
        warehouses: []
      }
    }
  },
  created() {
    this.getData()
  },
  methods: {
    getData: _.debounce(function () {
      axios.get('/api/inventory/warehouse-config', {
        params: {
          product_single: true,
          product_id    : this.ProductId
        }
      })
           .then(res => {
             this.product = res.data.locations
           })
    }, 200)
  }
}
</script>