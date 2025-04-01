<template>
  <div :id="id">
    <h3
        v-html="title"
        class="text-h6 text-center"
        v-if="title && typeof title==='string'"
    />
    <h5
        v-html="subtitle"
        class="subtitle text-center"
        v-if="subtitle && typeof subtitle==='string'"
    />
    <template v-if="(title && typeof title==='object') && title.length>0">
      <component
          v-text="t"
          :key="'tag-'+i"
          :is="i===0?'h3':'p'"
          v-for="(t, i) in title"
          :class="i===0 ? 'text-h6 text-center':'subtitle text-center'"
      />
    </template>
    <table class="table">
      <thead>
      <tr>
        <th v-for="(c, i) in columns" :key="i" v-html="c.text" v-if="hideColumn(c.value)"/>
      </tr>
      </thead>

      <tbody>
      <tr v-for="(r, i) in rows" :key="i+'r'">
        <td v-for="(c, i) in columns" v-if="hideColumn(c.value)" v-html="checkForValidData(r, c.value)">
          <!--          {{ checkForValidData(r[c.value], c.value) }}-->

        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name   : "sale-report-print",
  props  : {
    rows          : {
      type   : Array,
      default: () => []
    },
    title         : {
      default: () => ''
    },
    subtitle      : {
      default: () => ''
    },
    id            : {
      type   : String,
      default: () => 'list_print_bootstrap'
    },
    date_fields   : {
      type   : Array,
      default: () => []
    },
    remove_columns: {
      type   : Array,
      default: () => []
    },
    columns       : {
      type   : Array,
      default: () => []
    },
  },
  methods: {
    checkForValidData(val, column) {
      let data
      if(column === 'credit') {
        return _.sumBy(val.payments, 'paid')
      }
      if(column === 'warehouse') {
        let warehouses = this.$options.filters.uniqueByKeyValue(val.products, 'warehouse_name')
        return warehouses.map(w => w.warehouse_name).toString().replace(/,/g, "<br>")
      }
      if(column === 'supplier.company') {
        return this.$options.filters.obj_string_value(val, column)
      }
      if(column === 'balance') {
        return this.$root.$data.erp.report.checkPaymentStatus(val.total, val.payments)
      }
      if(column === 'products') {
        return val.products.map((p, i) => i+1+'.'+p.name).toString().replace(/,/g, "<br>")
      }
      if(column==='quantity'){
        return val.products.map((p, i) => i+1+'.'+p.quantityStr).toString().replace(/,/g, "<br>")
      }
      if(column==='base_quantity'){
        return val.products.map((p,i)=> i+1+'.'+p.quantityBaseUnit.toFixed(2)+p.unit.key).toString().replace(/,/g, "<br>")
      }
      if(column==='price'){
        return val.products.map((p,i)=>i+1+'.'+parseFloat(p.pivot.price).toFixed(2)).toString().replace(/,/g, "<br>")
      }
      if(column==='discount'){
        return val.products.map((p,i)=>i+1+'.'+parseFloat(p.pivot.discount).toFixed(2)).toString().replace(/,/g, "<br>")
      }
      else {
        data = val[column]
      }
      if(this.date_fields.length > 0 && this.date_fields.includes(column)) {
        data = this.$options.filters.removeTimeFromDate(val);
      }

      return data


    },
    hideColumn(val) {
      if(val === 'action') {
        return false
      } else if(this.remove_columns.includes(val)) {
        return false
      } else {
        return true
      }
    }
  }
}
</script>

<style scoped>

</style>