<template>
  <v-form @submit.prevent="submitExport()">
    <v-card>
      <v-card-title>
        Expense Payment Export
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col md="4" cols="12">
            <v-select
                label="format"
                v-model="formData.format"
                :error-messages="formErrors.format"
                :items="['xlsx', 'xls', 'csv', 'tsv', 'html']"
            >
            </v-select>
          </v-col>
          <v-col md="4" cols="12">
            <v-menu
                offset-y
                v-model="menu"
                min-width="290px"
                :nudge-right="40"
                transition="scale-transition"
                :close-on-content-click="false"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    readonly
                    clearable
                    v-on="on"
                    v-bind="attrs"
                    label="date range"
                    prepend-icon="event"
                    v-model="formData.date_from_to"
                    :error-messages="formErrors.date_from_to"
                >
                </v-text-field>
              </template>
              <v-date-picker
                  range
                  v-model="formData.date_from_to"
                  @input="formData.date_from_to.length>1?menu = false:null"
              >
              </v-date-picker>
            </v-menu>
          </v-col>
          <v-col md="4" cols="12">
            <v-text-field
                type="number"
                persistent-hint
                v-model="formData.rows"
                :error-messages="formErrors.rows"
                hint="highest number of rows 10000"
            ></v-text-field>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-btn color="success" type="submit">submit</v-btn>
      </v-card-actions>
    </v-card>
  </v-form>
</template>
<script>
export default {
  data() {
    return {
      menu      : false,
      formErrors: {
        format      : '',
        rows        : '',
        date_from_to: '',
      },
      formData  : {
        format      : 'xlsx',
        rows        : 1000,
        date_from_to: []
      }
    }
  },
  methods: {
    submitExport() {
      axios.get('/api/inventory/expense-payment-export',
                {
                  params      : this.formData,
                  responseType: 'blob'
                })
           .then(res => {
             const fileName = 'expense-bill-paid' + JSON.stringify(this.formData.date_from_to)
             const blobData = new Blob([res.data], {type: "application/" + this.formData.format})
             FileSaver.saveAs(blobData, fileName + '.' + this.formData.format)
             this.formData = {
               format      : 'xlsx',
               rows        : 1000,
               date_from_to: [],
             }
             this.$emit('closeDialogExport', res.status)
           })
           .catch(error => {
             Swal.fire({
                         icon: 'error',
                         text: error.response.statusText,
                       })
           })
    }
  }
}
</script>