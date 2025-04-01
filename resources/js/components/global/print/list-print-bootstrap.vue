<template>
  <div :id="id">
    <h3 class="text-h6 text-center" v-if="title && typeof title==='string'" v-html="title"></h3>
    <h5 class="subtitle text-center" v-if="subtitle && typeof subtitle==='string'" v-html="subtitle"></h5>

    <template v-if="(title && typeof title==='object') && title.length>0">
      <component
          :key="'tag-'+i"
          v-text="t"
          :is="i===0?'h3':'p'"
          v-for="(t, i) in title"
          :class="i===0 ? 'text-h6 text-center':'subtitle text-center'"
      />
    </template>
    <div class="text-center" v-if="other_text_arr.length>0">
      <p v-for="t in other_text_arr" v-html="t"></p>
    </div>
    <br>
    <table class="table">
      <thead>
      <tr>
        <th v-for="(c, i) in columns" :key="i" v-html="c.text" v-if="hideColumn(c.value)"/>
      </tr>
      </thead>
      <thead>
      <tr v-for="(r, i) in rows" :key="i+'r'">
        <td v-for="(c, i) in columns" v-if="hideColumn(c.value)">
<!--          {{ checkForValidData(r[c.value], c.value) }}-->
          {{ checkForValidData($options.filters.obj_string_value(r, c.value), c.value) }}
        </td>
      </tr>
      </thead>
    </table>
  </div>
</template>

<script>
export default {
  name   : "list-print-bootstrap",
  props  : {
    title         : {
      default: () => ''
    },
    id         : {
      type   : String,
      default: () => 'list_print_bootstrap'
    },

    subtitle      : {
      default: () => ''
    },
    rows          : {
      type   : Array,
      default: () => []
    },
    columns       : {
      type   : Array,
      default: () => []
    },
    other_text_arr: {
      type   : Array,
      default: () => []
    },
    remove_columns: {
      type   : Array,
      default: () => []
    },
    date_fields   : {
      type   : Array,
      default: () => []
    },
  },
  methods: {
    checkForValidData(val, column) {
      return this.date_fields.length > 0 && this.date_fields.includes(column)
          ? this.$options.filters.removeTimeFromDate(val)
          : val
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