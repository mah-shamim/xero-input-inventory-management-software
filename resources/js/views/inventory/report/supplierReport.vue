<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Supplier Report
        <v-spacer/>
        <v-btn icon @click="$htmlToPaper('list_print_bootstrap')">
          <v-icon>mdi-printer</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col>
            <v-text-field
                dense
                clearable
                label="id"
                hide-details
                v-model="options.code"
            />
          </v-col>
          <v-col>
            <v-text-field
                dense
                clearable
                hide-details
                label="phone"
                v-model="options.phone"
            />
          </v-col>
          <v-col>
            <v-text-field
                dense
                clearable
                hide-details
                label="company"
                v-model="options.company"
            />
          </v-col>
          <v-col>
            <v-text-field
                dense
                clearable
                hide-details
                label="name"
                v-model="options.name"
            />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="12" id="list_print_div">
            <v-data-table
                dense
                :headers="headers"
                :loading="loading"
                :items="items.data"
                class="elevation-0"
                :options.sync="options"
                :server-items-length="items.total"
                loading-text="Loading... Please wait"
                :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
            />
          </v-col>
        </v-row>
      </v-card-text>
      <list-print-bootstrap
          :title="'Supplier Report'"
          :columns="headers"
          :rows="items.data"
          style="visibility: collapse"
          :date_fields="[]"
      />
    </v-card>
  </v-container>
</template>

<script src="./supplierReport.js"></script>