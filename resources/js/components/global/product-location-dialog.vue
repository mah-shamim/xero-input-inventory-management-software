<template>
  <v-card>
    <v-card-title>{{product.name}}({{product.code}})</v-card-title>
    <v-card-text>
      <v-simple-table>
        <template v-slot:default>
          <thead>
          <tr>
            <th>Location</th>
            <th>Quantity</th>
            <th>Weight</th>
          </tr>
          </thead>
          <tbody>
          <template v-for="(location, key) in items.location">
            <tr
                v-if="location.quantity"
            >
              <td>
                {{key}}
              </td>
              <td>
                {{location.quantity}}
              </td>
              <td>
                {{location.weight}}
              </td>
            </tr>
          </template>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
    <v-card-actions></v-card-actions>
  </v-card>
</template>

<script>
import _ from "lodash";

export default {
  name   : "product-location-dialog",
  props  : {
    warehouseId: {
      type   : Number,
      default: null
    },
    productId: {
      type   : Number,
      default: null
    }
  },
  data() {
    return {
      items: {},
      product:{}
    }
  },
  created() {
    this.getWarehouse()
  },
  methods: {
    getWarehouse: _.debounce(function () {
      axios.get('/api/report/warehouses', {
        params: {
          warehouse_id    : this.warehouseId,
          product_location: true,
          product_id:this.productId
        }
      })
           .then(res => {
             this.items = res.data.warehouse
             this.product = res.data.product
             if(!_.isEmpty(this.items) && _.has(this.items, 'location')){
               this.items.location = !_.isEmpty(this.items.location) ? JSON.parse(this.items.location) : [];
             }
             
           })
    }, 200)
  }
}
</script>
<!--page: 1-->
<!--itemsPerPage: 15-->
<!--mustSort: false-->
<!--multiSort: false-->
<!--warehouse_id: 12-->
<!--http://localhost:3000/api/report/warehouses?page=1&itemsPerPage=15&mustSort=false&multiSort=false&warehouse_id=12-->
<style scoped>

</style>