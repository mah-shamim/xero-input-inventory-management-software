<template>
  <div id="create_department_dialog">
    <form @submit.prevent="submitForm()" class="dense-form">
      <v-card>
        <v-card-title>
          {{ !_.isEmpty(forms) && forms.id ? 'Edit' : 'Create' }} Department
        </v-card-title>
        <v-divider/>
        <v-card-text class="fill-height">
          <v-text-field
              dense
              outlined
              dusk="name"
              name="name"
              label="name"
              v-model="forms.name"
              :error-messages="formError.name"
          />
        </v-card-text>
        <v-divider/>
        <v-card-actions>
          <v-btn
              dusk="submit"
              color="primary"
              type="submit"
          >
            submit
          </v-btn>
        </v-card-actions>
      </v-card>
    </form>
  </div>
</template>
<script>
export default {
  props: {
    forms: {
      type: Object,
      default: {}
    }
  },
  data: () => ({
    formError: {}
  }),
  methods: {
    submitForm() {
      let url = '/api/payroll/department'
      let method = 'post'
      let successMessage = 'Department has been created successfully'
      if (!_.isEmpty(this.forms) && this.forms.id) {
        url = url + '/' + this.forms.id
        method = 'patch'
        successMessage = 'Department has been updated successfully'
      }
      axios[method](url, this.forms)
          .then(res => {
            swal.fire({
              icon: 'success',
              timer: 2000,
              text: successMessage
            })
                .then(() => {
                  this.formError = {}
                  this.forms.name = ''
                  this.$emit('createSuccess', true)
                })
          })
          .catch(error => {
            this.formError = error.response.data.errors
          })

    }
  }
}
</script>