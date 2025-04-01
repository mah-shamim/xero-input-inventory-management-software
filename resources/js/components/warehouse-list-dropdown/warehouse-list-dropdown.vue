<template>
  <div class="warehouse-list-dropdown">
    <v-autocomplete
        cache-items
        item-value="id"
        name="warehouse_id"
        item-text="name"
        label="warehouse"
        :items="warehouses"
        data-vv-as="warehouse"
        v-validate="`${required}`"
        v-model="forms.warehouse_id"
        :loading="warehouses.length<=0"
        :filter="$options.filters.customFilter"
        :error-messages="errors.collect('forms.warehouse_id')"
    >
      <template v-slot:item="data">
        <template v-if="typeof data.item !== 'object'">
          <v-list-item-content v-text="data.item"></v-list-item-content>
        </template>
        <template v-else>
          <v-list-item-content v-text="data.item.name"/>
          <v-list-item-action v-text="data.item.code"/>
        </template>
      </template>
    </v-autocomplete>
  </div>
</template>

<script>
export default {

  props: ['forms', 'created', 'required'],
  data: () => ({
    warehouses: [],
  }),
  created() {
    if (this.created) {
      axios.get('/api/inventory/productdamages/create')
          .then(res => {
            this.warehouses = res.data.warehouses
          });
    }
  },

};
</script>
