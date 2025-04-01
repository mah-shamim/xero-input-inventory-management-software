<template>
  <v-container fluid>
    <v-card>
      <v-card-title>
        Employees
        <v-spacer/>
        <!--      <back-button/>-->
        <!--      v-if="$options.filters.checkPermission(['employee', 'create'])"-->
        <tooltip-button
            icon
            dark
            dusk="create"
            color="primary"
            position="bottom"
            data-text="Create Employee"
            :icon-name="$root.icons.create"
            @click="createOrUpdateDialog=true"
        />
        <v-btn icon @click="$htmlToPaper('list_print_bootstrap')">
          <v-icon>mdi-printer</v-icon>
        </v-btn>
      </v-card-title>
      <!--    v-if="$options.filters.checkPermission(['employee', 'index'])"-->
      <v-card-text>
        <v-row>
          <v-col>
            <v-text-field
                dense
                label="mobile no"
                hide-details
                v-model="options.mobile"
            />
          </v-col>
          <v-col>
            <v-text-field
                dense
                hide-details
                @click.stop
                label="employee id"
                v-model="options.employee_id"
            />
          </v-col>
          <v-col>
            <v-text-field
                dense
                label="name"
                @click.stop
                hide-details
                v-model="options.name"
            />
          </v-col>
        </v-row>
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
          <template v-slot:[`item.salary`]="{item}">
            {{ item.salary|toFix(2) }}
          </template>
          <template v-slot:[`item.join_date`]="{item}">
            {{ item.join_date | removeTimeFromDate }}
          </template>
          <!--        v-if="$options.filters.checkPermission(['employee', 'edit']) ||$options.filters.checkPermission(['employee', 'delete'])"-->
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
                <!-- v-if="$options.filters.checkPermission(['employee', 'view'])"-->
                <v-list-item>
                  <tooltip-button
                      icon
                      size="small"
                      icon-name="mdi-eye"
                      data-text="View"
                      color="success"
                  />
<!--                      :to="{name:'payroll.employee.show',params:{id:item.id}}"-->
                </v-list-item>
                <!-- v-if="$options.filters.checkPermission(['employee', 'edit'])"-->
                <v-list-item>
                  <tooltip-button
                      icon
                      icon-name="edit"
                      data-text="Edit"
                      size="small"
                      :dusk="`edit-${index}`"
                      color="primary"
                      @click="createOrUpdateDialog=true, edit_id=item.id"
                  />
                </v-list-item>
                <!-- v-if="$options.filters.checkPermission(['employee', 'delete'])" -->
                <v-list-item
                >
                  <tooltip-button
                      icon
                      size="small"
                      icon-name="delete"
                      data-text="Delete"
                      color="red"
                      :dusk="`delete-${index}`"
                      @click="deleteItem(item.id)"
                  />
                </v-list-item>
              </v-list>
            </v-menu>

          </template>
        </v-data-table>
      </v-card-text>
      <list-print-bootstrap
          :title="'Employees List'"
          :columns="headers"
          :rows="items.data"
          style="visibility: collapse"
          :date_fields="['join_date']"
      >
      </list-print-bootstrap>
      <v-dialog
          scrollable
          width="1200"
          v-model="createOrUpdateDialog"
      >
        <employee-create
            :edit-id="edit_id"
            v-if="createOrUpdateDialog"
            v-model="createOrUpdateDialog"
        />
      </v-dialog>
      <!--    <unauthorize-message-->
      <!--        v-if="!$options.filters.checkPermission(['warehouse', 'index'])"-->
      <!--    />-->
    </v-card>
  </v-container>
</template>

<script src="./index.js"></script>
