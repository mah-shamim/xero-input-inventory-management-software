<template>
  <v-dialog v-model="showModal">
    <form @submit.prevent="submit()">
      <v-card outlined>
        <v-card-title>Purchase Return</v-card-title>
        <v-card-text>
          <v-simple-table dense>
            <template v-slot:default>
              <thead>
              <tr>
                <th>No.</th>
                <th>Code</th>
                <th>Name</th>
                <th>Warehouse</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Discount</th>
                <th>Total</th>
                <th>status</th>
                <th>Amount</th>
                <th class="text-center">unit</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(product, index) in rproduct">
                <td>
                  {{ index + 1 }}
                </td>
                <td>{{ product.code }}</td>
                <td>{{ product.name }}</td>
                <td>
                  <v-autocomplete
                      item-value="id"
                      persistent-hint
                      item-text="name"
                      label="warehouse"
                      :items="warehouses"
                      hint="select warehouse"
                      v-model="product.pivot.warehouse_id"
                  >
                  </v-autocomplete>
                </td>
                <td>{{ product.quantityStr }}</td>
                <td>{{ product.pivot.price }}</td>
                <td>{{ product.pivot.discount }}</td>
                <td id="return_total">{{ product.pivot.subtotal }}</td>
                <td>{{ $root.productStatus(product.status) }}</td>
                <td>
                  <v-text-field
                      type="number"
                      step="any"
                      dusk="total"
                      v-model="product.ramount"
                      class="input-item"
                  >
                  </v-text-field>
                </td>
                <td>
                  <table class="table striped">
                    <tr>
                      <td v-for="(unit, index) in product.units">
                        <v-text-field
                            step="any"
                            :dusk="`unit-${index}`"
                            type="number"
                            persistent-hint
                            :hint="unit.key"
                            :label="unit.key"
                            v-model="unit.runit"
                        >
                        </v-text-field>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
        <v-card-actions>
          <v-btn type="submit" dusk="submit">submit</v-btn>
        </v-card-actions>
      </v-card>
    </form>
  </v-dialog>
</template>

<script src="./returns.js"></script>