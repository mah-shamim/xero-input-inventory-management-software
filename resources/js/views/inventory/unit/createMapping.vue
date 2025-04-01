<template>
  <v-dialog v-model="showModal" max-width="600px">
    <form @submit.prevent="postItem('forms')" data-vv-scope="forms">
      <v-card>
        <v-card-title>Create Unit</v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="12" md="12">
              <v-autocomplete
                  :error-messages="errors.collect('forms.from_unit_id')"
                  v-model="forms.from_unit_id"
                  data-vv-name="from_unit_id"
                  v-validate="'required'"
                  name="from_unit_id"
                  dusk="from_unit_id"
                  :items="dropdowns"
                  label="from unit"
                  item-text="key"
                  item-value="id"
                  single-line
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12" sm="12" md="12">
              <v-text-field
                  :error-messages="errors.collect('forms.from_unit_val')"
                  v-model="forms.from_unit_val"
                  data-vv-name="from_unit_val"
                  v-validate="'required'"
                  name="from_unit_val"
                  dusk="from_unit_val"
                  persistent-hint
                  label="value*"
                  type="number"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="12" md="12">
              <v-autocomplete
                  :error-messages="errors.collect('forms.to_unit_id')"
                  v-model="forms.to_unit_id"
                  data-vv-name="to_unit_id"
                  v-validate="'required'"
                  :items="dropdowns"
                  name="to_unit_id"
                  dusk="to_unit_id"
                  label="to unit"
                  item-text="key"
                  item-value="id"
                  single-line
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12" sm="12" md="12">
              <v-text-field
                  type="number"
                  label="value*"
                  persistent-hint
                  name="to_unit_val"
                  dusk="to_unit_val"
                  v-validate="'required'"
                  data-vv-name="to_unit_val"
                  v-model="forms.to_unit_val"
                  :error-messages="errors.collect('forms.to_unit_val')"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions justify="flex-end">
          <v-btn color="blue darken-1" text @click="$emit('input', false)">
            Close
          </v-btn>
          <v-btn
              color="success darken-1"
              text
              type="submit"
              success
              dusk="submit"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </form>
  </v-dialog>
</template>
<script>
export default {
  props: {
    modelId: {
      type: Number,
      default: null
    },
    value: {
      type: Boolean,
      default: false
    },
  },
  data() {
    return {
      forms: {
        id: null
      },
      dropdowns: []
    }
  },
  computed: {
    showModal: {
      get() {
        return this.value;
      },
      set(value) {
        if (!value) this.$emit('input', value)
      }
    }
  },
  created() {
    this.getData()
  },
  methods: {
    getData() {
      let url = this.modelId ? api_base_url + '/unitconversions/' + this.modelId + '/edit' : api_base_url + '/unitconversions/create';
      axios.get(url)
          .then(res => {
            this.dropdowns = res.data.units
          })
          .catch(err => {

          })
    },
    postItem(scope) {
      this.$validator.validateAll(scope).then(result => {
        if (result) {
          !this.forms.id ? this.saveItem(scope) : this.updateItem(scope)
        }
      })
    },
    editItem(id) {
      axios.get('/api/inventory/unitconversions/' + id + '/edit')
          .then(res => {
            console.log(res.data)
          })
          .catch(err => {

          })
    },
    saveItem(scope) {
      axios.post('/api/inventory/unitconversions', this.forms)
          .then(res => {
            swal({
              type: res.data.type,
              timer: 2000,
              text: res.data.message,
              showCancelButton: false,
              showConfirmButton: false,
            }).catch(swal.noop)
            this.dialog = false
            if (res.data.type === 'success') {
              this.$emit('input', false)
            }
          }).catch(error => {
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
    },
  }
}
</script>