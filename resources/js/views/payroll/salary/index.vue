<template>
  <v-card outlined>
    <!--    <v-dialog v-model="exportDialog" width="800">-->
    <!--      <salary-export-->
    <!--          @closeDialogExport="closeDialogExport"-->
    <!--      >-->
    <!--      </salary-export>-->
    <!--    </v-dialog>-->
    <v-card-title>
      Salaries
      <v-spacer/>

      <!--          v-if="$options.filters.checkPermission(['salary', 'create'])"-->
      <tooltip-button
          icon
          dark
          dusk="create"
          color="primary"
          position="bottom"
          data-text="Create Salary"
          :icon-name="$root.icons.create"
          @click="createOrUpdateDialog=true"
      />

      <!--          v-if="$options.filters.checkPermission(['salary', 'export'])"-->
      <v-btn
          icon
          @click="exportDialog=true"
      >
        <v-icon>mdi-database-export</v-icon>
      </v-btn>

      <!--          v-if="$options.filters.checkPermission(['salary', 'import'])"-->
<!--      <v-tooltip-->
<!--          bottom-->
<!--      >-->
<!--        <template v-slot:activator="{ on, attrs }">-->
<!--          <v-btn v-bind="attrs" v-on="on" icon>-->
<!--            <v-icon @click="$refs.inputUpload.click()">mdi-database-import</v-icon>-->
<!--            <input-->
<!--                type="file"-->
<!--                v-show="false"-->
<!--                id="inputUpload"-->
<!--                ref="inputUpload"-->
<!--                @change="uploadFile"-->
<!--            >-->
<!--          </v-btn>-->
<!--        </template>-->
<!--        <span>import</span>-->
<!--      </v-tooltip>-->
      <v-btn icon @click="$htmlToPaper('list_print_bootstrap')">
        <v-icon>mdi-printer</v-icon>
      </v-btn>
    </v-card-title>

    <!--        v-if="$options.filters.checkPermission(['warehouse', 'index'])"-->
    <v-card-text
    >
      <v-row>
        <v-col md="3" cols="12">
          <v-text-field
              label="employee"
              v-model="options.employee"
          >
          </v-text-field>
        </v-col>
        <v-col md="3" cols="12">
          <v-menu
              offset-y
              v-model="menu1"
              :nudge-right="40"
              min-width="290px"
              transition="scale-transition"
              :close-on-content-click="false"
          >
            <template v-slot:activator="{ on }">
              <v-text-field
                  readonly
                  v-on="on"
                  clearable
                  persistent-hint
                  name="salary_date"
                  label="salary date"
                  prepend-icon="event"
                  hint="type salary date"
                  v-model="options.salary_date"
              >
              </v-text-field>
            </template>
            <v-date-picker
                @input="menu1 = false"
                v-model="options.salary_date"
            >
            </v-date-picker>
          </v-menu>
        </v-col>
        <v-col md="3" cols="12">
          <v-menu
              offset-y
              v-model="menu2"
              :nudge-right="40"
              min-width="290px"
              transition="scale-transition"
              :close-on-content-click="false"
          >
            <template v-slot:activator="{ on }">
              <v-text-field
                  readonly
                  v-on="on"
                  persistent-hint
                  name="salary_month"
                  label="salary month"
                  prepend-icon="event"
                  hint="type salary month"
                  v-model="options.salary_month"
              >
              </v-text-field>
            </template>
            <v-date-picker
                type="month"
                @input="menu2 = false"
                v-model="options.salary_month"
            >
            </v-date-picker>
          </v-menu>
        </v-col>
        <v-col md="3" cols="12">
          <v-select
              clearable
              item-value="id"
              item-text="name"
              label="department"
              :items="departments"
              v-model="options.department"
          ></v-select>
        </v-col>
        <v-col md="12">
          <v-data-table
              :headers="headers"
              :loading="loading"
              :items="items.data"
              class="elevation-0"
              :options.sync="options"
              :server-items-length="items.total"
              loading-text="Loading... Please wait"
              :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
          >
            <template v-slot:top>
              <v-row class="px-3">
                <v-col md="5">
                  <v-card
                      class="mx-auto"
                  >
                    <v-list-item two-line>
                      <v-list-item-content class="text-center">
                        <v-list-item-title class="text-h5">Total</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>

                    <v-card-text>
                      <v-row align="center">
                        <v-col md="4">
                          total Salary
                        </v-col>
                        <v-col class="text-h2" md="8">
                          {{ total_amount.total_current_salary }}
                        </v-col>
                        <v-col md="4">
                          total paid
                        </v-col>
                        <v-col md="8">{{ total_amount.total_paid }}</v-col>
                        <v-col md="4">
                          total salary payable
                        </v-col>
                        <v-col md="8">{{ total_amount.total_due }}</v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-col>
                <v-col md="7">
                  <salary-chart :total="total_amount" v-if="total_amount.total_paid>0"></salary-chart>
                </v-col>
              </v-row>
            </template>
            <template v-slot:[`item.total`]="{item}">
              <router-link
                  :to="{
              name: 'bank.transaction',
              params: { id: item.transaction_id },
            }"
                  v-if="item.transaction_id"
              >
                {{ item.total }}
              </router-link>
              <template v-else>
                {{ item.total }}
              </template>
            </template>
            <template v-slot:item.salary_month="{ item }">
              {{ item.salary_month | momentFormatBy('MMM YYYY') }}
            </template>
            <template v-slot:item.salary_date="{ item }">
              {{
                item.salary_date | momentFormatByWithCurrentFormat($store.state.settings.settings.date_format,'DD MMM, YYYY')
              }}
            </template>

            <!--                v-if="$options.filters.checkPermission(['salary', 'edit']) || $options.filters.checkPermission(['salary', 'delete'])"-->
            <template
                v-slot:item.action="{ item, index }"
            >
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

                  <!--                      v-if="$options.filters.checkPermission(['salary', 'edit'])"-->
                  <v-list-item
                  >
                    <tooltip-button
                        icon
                        size="small"
                        color="primary"
                        icon-name="edit"
                        data-text="Edit"
                        :dusk="`edit-${index}`"
                        @click="createOrUpdateDialog=true, edit_id=item.id"
                    />
                  </v-list-item>

                  <!--                      v-if="$options.filters.checkPermission(['salary', 'delete'])"-->
                  <v-list-item
                  >
                    <tooltip-button
                        icon
                        color="red"
                        size="small"
                        icon-name="delete"
                        data-text="Delete"
                        :dusk="`delete-${index}`"
                        @click="deleteItem(item.id)"
                    />
                  </v-list-item>
                </v-list>
              </v-menu>


            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-card-text>
    <list-print-bootstrap
        :title="'Salaries List'"
        :columns="headers"
        :rows="items.data"
        style="visibility: collapse"
        :date_fields="['salary_date','salary_month']"
        :remove_columns="['created_at']"
    >
    </list-print-bootstrap>
    <salary-create
        :model-id="edit_id"
        v-if="createOrUpdateDialog"
        v-model="createOrUpdateDialog"
    ></salary-create>

    <!--    <unauthorize-message-->
    <!--        v-if="!$options.filters.checkPermission(['salary', 'index'])"-->
    <!--    />-->
  </v-card>
</template>
<script src="./index.js"></script>
