<!--@todo dusk test is working-->
<!--@todo error message should be there-->
<script>
export default {
  name: "vuetify-date-time",
  props: {
    value: {
      required: true
    },
    required: {
      type: Boolean,
      default: false,
      required: false
    },
    label: {
      type: String,
      default: 'Date',
      required: false
    },
    name: {
      type: String,
      required: false
    },
    errorKeyName: {
      type: String,
      default: 'forms.purchase_date'
    }
  },
  data: () => ({
    dateDialog: false
  }),
  computed: {
    date_name() {
      return !this.name
          ? this.label.replace(' ', '_').toLowerCase()
          : this.name.replace(' ', '_').toLowerCase();
    },
    date: {
      get() {
        return this.value;
      },
      set(value) {
        if (value) this.$emit('input', value)
      }
    }
  }
}
</script>

<template>
  <v-menu
      offset-y
      :nudge-right="40"
      min-width="290px"
      v-model="dateDialog"
      transition="scale-transition"
      :close-on-content-click="false"
  >
    <template v-slot:activator="{ on }">
      <v-text-field
          dense
          readonly
          v-on="on"
          :label="label"
          v-model="date"
          :dusk="date_name"
          prepend-icon="mdi-calendar"
      />
    </template>
    <v-date-picker
        v-model="date"
        :name="date_name"
        :data-vv-name="date_name"
        @input="dateDialog = false"
        v-validate="{required: required}"
        :error-messages="errors.collect(errorKeyName)"
    />
  </v-menu>
</template>

<style scoped>

</style>