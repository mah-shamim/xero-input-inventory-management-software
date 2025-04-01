<template>
  <v-row class="ma-auto">
    <v-col md="3" cols="12">
      <v-card>
        <v-card-title>
          <vue-dropzone
              id="dropzone"
              ref="myVueDropzone"
              :useCustomSlot=true
              :options="dropzoneOptions"
              @vdropzone-error="uploadError"
              @vdropzone-success="uploadSuccess"
          >
            <div class="dropzone-custom-content" v-if="!_.isEmpty(employee)">
              <v-avatar size="150">
                <img
                    alt="user"
                    :src="employee.avatar"
                >
              </v-avatar>
              <p>click to upload</p>
              <span class="text-caption">500X500 images</span>
            </div>
          </vue-dropzone>
          <p class="ml-3">
            {{ employee.name }}
          </p>
        </v-card-title>
        <v-card-text>
          <strong>Office Information</strong>
          <v-divider></v-divider>
          <p class="mt-1">Id: {{ employee.employee_id }}</p>
          <p>Designation: {{ employee.designation }}</p>
          <p>contract type: {{ employee.contract_type }}</p>
          <p>salary: {{ employee.salary | tofix(2) }}</p>
          <p>join date: {{ employee.join_date | removeTimeFromDate }}</p>
          <p>record created at: {{ employee.created_at | removeTimeFromDate }}</p>

          <strong>Personal Information:</strong>
          <v-divider></v-divider>
          <p class="mt-1">nid: {{ employee.nid }}</p>
          <p>emergency contact: {{ employee.emergency }}</p>
          <p>mobile: {{ employee.mobile }}</p>
          <p>address: {{ employee.address }}</p>
          <p>birth day: {{ employee.birth | removeTimeFromDate }}</p>
        </v-card-text>
      </v-card>
    </v-col>
    <v-col md="9" cols="12">

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
        <template v-slot:item.salary_month="{ item }">
          {{ item.salary_month | momentFormatBy('MMM YYYY') }}
        </template>
        <template v-slot:item.salary_date="{ item }">
          {{
            item.salary_date | momentFormatByWithCurrentFormat($root.settings.settings.date_format,'DD MMM, YYYY')
          }}
        </template>
      </v-data-table>
    </v-col>
  </v-row>
</template>
<script src="./show.js"></script>