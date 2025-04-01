<template>
  <v-container fluid>
    <v-row>
      <v-col md="12" cols="12">
        <v-dialog
            scrollable
            v-model="create_dialog"
            width="500"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                icon
                dark
                v-on="on"
                dusk="create"
                v-bind="attrs"
                color="primary"
                class="float-right"
            >
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </template>
          <department-create
              @createSuccess="createSuccess"
              :forms="forms"
          />
        </v-dialog>
        <v-btn class="float-right" icon @click="$htmlToPaper('list_print_bootstrap')">
          <v-icon>mdi-printer</v-icon>
        </v-btn>
      </v-col>
    </v-row>
    <v-data-table
        :items="items.data"
        :loading="loading"
        :headers="headers"
        class="elevation-0"
        :options.sync="options"
        :server-items-length="items.total"
        loading-text="Loading... Please wait"
        :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
    >
      <template v-slot:top>
        <h3 class="pa-3">Departments</h3>
      </template>
      <template v-slot:[`item.created_at`]="{item}">
        {{ item.created_at | removeTimeFromDate }}
      </template>
      <!-- v-if="$options.filters.checkPermission(['department', 'edit']) ||$options.filters.checkPermission(['department', 'delete'])"-->
      <template
          v-slot:item.action="{ item, index }"
      >
        <!--        <v-btn-->
        <!--            icon-->
        <!--            small-->
        <!--            color="success"-->
        <!--            :to="{name:'payroll.department.show',params:{id:item.id}}"-->
        <!--            v-if="$options.filters.checkPermission(['department', 'view'])"-->
        <!--        >-->
        <!--          <v-icon small>mdi-eye</v-icon>-->
        <!--        </v-btn>-->
        <v-menu top :close-on-content-click="closeOnContentClick">
          <template v-slot:activator="{ on }">
            <v-btn
                dark
                x-small
                v-on="on"
                dusk="action"
                color="primary"
            >
              action
            </v-btn>
          </template>
          <v-list>
            <v-list-item>
              <!--                  v-if="$options.filters.checkPermission(['department', 'edit'])"-->
              <v-btn
                  icon
                  small
                  color="primary"
                  :dusk="`edit-${index}`"
                  @click="editDepartment(item.id)"
              >
                <v-icon small>edit</v-icon>
              </v-btn>
            </v-list-item>
            <!--                  v-if="$options.filters.checkPermission(['department', 'delete'])"-->
            <v-list-item>
              <v-btn
                  icon
                  small
                  color="red"
                  :dusk="`delete-${index}`"
                  @click="deleteDepartment(item.id)"
              >
                <v-icon small>delete</v-icon>
              </v-btn>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
    </v-data-table>
    <list-print-bootstrap
        :title="'Department List'"
        :columns="headers"
        :rows="items.data"
        style="visibility: collapse"
        :date_fields="['created_at']"
    >
    </list-print-bootstrap>
  </v-container>
</template>

<script>
import DepartmentCreate from './create.vue'
import _ from "lodash";

const url = '/api/payroll/department'
export default {
  name: "department",
  components: {
    DepartmentCreate
  },
  data() {
    return {
      forms: {},
      create_dialog: false,
      loading: false,
      items: {},
      options: {
        sortBy: ['name'],
        sortDesc: [true],
        itemsPerPage: this.$store.state.itemsPerPage,
      },
      headers: [
        {text: 'name', value: 'name'},
        {text: 'employee count', value: 'employees_count'},
        {text: 'total salary paid', value: 'sum_salaries'},
        {text: 'created at', value: 'created_at'},
        {text: 'action', value: 'action', sortable: false},
      ],
      closeOnContentClick: false
    }
  },

  watch: {
    create_dialog(val) {
      if (!val) {
        this.forms = {}
      }
    },
    options: {
      deep: true,
      handler() {
        this.loading = true
        this.getData()
      }
    }
  },

  methods: {
    deleteDepartment(id) {
      this.loading = true
      this.$deleteWithConfirmation({
        text: 'Are you sure you want delete this department?',
        url: url + '/' + id
      })
          .then(data => {
            this.getData()
            this.loading = false
          })
          .catch(error => {
            this.loading = false
          })
    },
    editDepartment(id) {
      axios.get(url + '/' + id + '/edit')
          .then(res => {
            this.forms = res.data.department
            this.$nextTick(() => {
              this.create_dialog = true
            })
          })
    },
    getData: _.debounce(function () {
      this.loading = true
      axios.get(url, {
        params: this.options
      })
          .then(res => {
            this.items = res.data.departments
            if (!_.isEmpty(this.items) && this.items.data.length > 0) {
              this.items.data.forEach(i => {
                if (i.sum_salaries) {
                  i.sum_salaries = Number(i.sum_salaries).toFixed(2)
                }
              })
            }
            this.loading = false
          })
    }, 400),
    createSuccess(val) {
      if (val) {
        this.create_dialog = false
        this.getData()
      }
    }
  }
}
</script>

<style scoped>

</style>