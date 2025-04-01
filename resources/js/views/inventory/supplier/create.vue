<script>
export default {
  name: "create",
  props: {
    value: {
      type: Boolean,
      required: true,
      default: false
    },
    id: {
      type: Number,
      default: null
    }
  },
  data() {
    return {
      forms: {
        id: null,
        name: '',
        email: '',
        phone: '',
        company: '',
        address: ''
      }
    }
  },
  computed: {
    email() {
      return email
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
  watch: {
    id: {
      immediate: true,
      handler(val) {
        if (val) {
          this.editItem()
        }
      }
    }
  },
  methods: {
    postItem(scope) {
      this.$validator.validateAll(scope).then(result => {
        if (result) {
          !this.forms.id ? this.saveItem(scope) : this.updateItem(scope)
        }
      })
    },
    saveItem(scope) {
      axios.post('/api/inventory/suppliers', this.forms)
          .then(res => {
            swal({
              type: res.data.type,
              timer: 2000,
              text: res.data.message,
              showCancelButton: false,
              showConfirmButton: false,
            }).catch(swal.noop)
            if (res.data.type === 'success') {
              this.$emit('input', false)
              this.$emit('getNewSupplier', res.data.supplier)
            }
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
    },
    editItem() {
      axios.get('/api/inventory/suppliers/' + this.id + '/edit')
          .then(res => {
            this.forms = res.data.suppliers
          })
    },
    updateItem(scope) {
      this.forms.id = this.id
      axios.patch('/api/inventory/suppliers/' + this.forms.id, this.forms)
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
              this.$emit('getNewSupplier', res.data.supplier)
            }
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
    },
  }
}
</script>

<template>

  <v-dialog v-model="showModal" max-width="800px">
    <form @submit.prevent="postItem('forms')" data-vv-scope="forms">
      <v-card>
        <v-card-title>
          <span class="headline">Supplier</span>
        </v-card-title>
        <v-card-text>
          <v-container>

            <v-row>
              <v-col cols="12" sm="4" md="4">
                <v-text-field
                    v-model="forms.name"
                    v-validate="{required:true}"
                    :error-messages="errors.collect('forms.name')"
                    data-vv-name="name"
                    dusk="name"
                    hint="bob, jack, karim"
                    label="name"
                    name="name"
                    type="text"
                    persistent-hint
                />
              </v-col>
              <v-col cols="12" sm="4" md="4">
                <v-text-field
                    v-model="forms.code"
                    v-validate="{required:true}"
                    :error-messages="errors.collect('forms.code')"
                    data-vv-name="code"
                    dusk="code"
                    hint="123id"
                    label="code"
                    name="code"
                    type="text"
                    persistent-hint
                />
              </v-col>
              <v-col cols="12" sm="4" md="4">
                <v-text-field
                    v-model="forms.email"
                    v-validate="'required|email'"
                    :error-messages="errors.collect('forms.email')"
                    data-vv-name="email"
                    dusk="email"
                    label="email"
                    name="email"
                    persistent-hint
                    type="email"
                />
              </v-col>
              <v-col cols="12" sm="4" md="4">
                <v-text-field
                    v-model="forms.phone"
                    v-validate="{required:true}"
                    :error-messages="errors.collect('forms.phone')"
                    data-vv-name="phone"
                    dusk="phone"
                    label="phone"
                    name="phone"
                    persistent-hint
                />
              </v-col>
              <v-col cols="12" sm="4" md="4">
                <v-text-field
                    v-model="forms.company"
                    v-validate="{required:true}"
                    :error-messages="errors.collect('forms.company')"
                    data-vv-name="phone"
                    dusk="company"
                    label="company"
                    name="company"
                    persistent-hint
                />
              </v-col>
              <v-col cols="12" sm="8" md="8">
                <v-textarea
                    v-model="forms.address"
                    :error-messages="errors.collect('forms.address')"
                    data-vv-name="address"
                    dusk="address"
                    label="address"
                    name="address"
                    persistent-hint
                    phone="address"
                />
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn
              color="blue darken-1"
              outlined
              @click="showModal = false"
          >
            Close
          </v-btn>
          <v-btn
              color="success darken-1"
              outlined
              type="submit"
              dusk="submit"
              success
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </form>
  </v-dialog>

</template>

<style scoped>

</style>