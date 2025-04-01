<template>
  <v-container fluid id="warehouse_index">
    <v-card flat>
      <v-card-title>
        Warehouses, Total {{ warehouses.total }} testing docker
        <v-spacer/>
        <v-text-field
            v-model="options.search"
            :append-icon="$root.icons.search"
            label="Search"
            single-line
            hide-details
        />
        <action-btn
            text="Create"
            dusk="create"
            :icon="$root.icons.create"
            @click="dialog=true"
        />
        <collapse-btn @click="dense=!dense"/>
      </v-card-title>
      <v-card-text>
        <v-data-table
            :headers="headers"
            :dense="dense"
            :items="warehouses.data"
            :loading="loading"
            :server-items-length="warehouses.total"
            loading-text="Loading... Please wait"
            :options.sync="options"
            :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
        >
          <template v-slot:item.action="{ item, index }">

            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                    @click="editWarehouse(item.id)"
                    :dusk="`edit-${index}`"
                    small
                    v-on="on"
                    fab
                    text
                    color="success"
                >
                  <v-icon small v-text="$root.icons.edit"/>
                </v-btn>
              </template>
              <span>Edit</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                    @click="deleteWarehouse(item.id)"
                    :dusk="`delete-${index}`"
                    small
                    v-on="on"
                    fab
                    text
                    color="red"
                >
                  <v-icon small v-text="$root.icons.delete"/>
                </v-btn>
              </template>
              <span>Delete</span>
            </v-tooltip>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
    <v-dialog v-model="dialog" max-width="800px">
      <template v-slot:activator="{ on }">
        <v-btn dusk="open_warehouse_dialog" dark text v-on="on" @click="forms={}">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </template>
      <v-card>
        <form @submit.prevent="postWarehouse('forms')" data-vv-scope="forms">
          <v-card-title>
            <span class="headline">Warehouse</span>
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" sm="6" md="6">
                <v-text-field
                    label="name"
                    hint="ex. main, showroom"
                    persistent-hint
                    name="name"
                    dusk="name"
                    v-model="forms.name"
                    v-validate="'required'"
                    :error-messages="errors.collect('forms.name')"
                    data-vv-name="name"
                    required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" md="6">
                <v-text-field
                    label="code"
                    persistent-hint
                    name="code"
                    dusk="code"
                    v-model="forms.code"
                    data-vv-name="code"
                    :error-messages="errors.collect('forms.code')"
                    v-validate="'required'"
                    required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" md="6">
                <v-text-field
                    label="email"
                    persistent-hint
                    name="email"
                    dusk="email"
                    v-model="forms.email"
                    data-vv-name="email"
                    v-validate="'required|email'"
                    type="email"
                    :error-messages="errors.collect('forms.email')"
                    required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" md="6">
                <v-text-field
                    label="phone"
                    persistent-hint
                    name="phone"
                    dusk="phone"
                    v-model="forms.phone"
                    data-vv-name="phone"
                    v-validate="'required'"
                    :error-messages="errors.collect('forms.phone')"
                    required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="8" md="8">
                <v-text-field
                    label="address"
                    persistent-hint
                    name="address"
                    dusk="address"
                    v-model="forms.address"
                    data-vv-name="address"
                    :error-messages="errors.collect('forms.address')"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="4" md="4">
                <v-checkbox
                    name="default"
                    dusk="default"
                    label="default"
                    data-vv-name="address"
                    v-model="forms.is_default"
                    :error-messages="errors.collect('forms.address')"
                />
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions>
            <v-btn color="success" outlined text type="submit" dusk="submit" success>
              Save
            </v-btn>
            <v-btn color="blue" text outlined @click="dialog = false">
              Close
            </v-btn>
          </v-card-actions>
        </form>
      </v-card>
    </v-dialog>
  </v-container>
</template>
<script src="./js/index.js"></script>