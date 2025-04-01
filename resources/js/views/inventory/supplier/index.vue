<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Suppliers, Total {{ items.total }}
        <v-spacer></v-spacer>
        <v-text-field
            v-model="options.search"
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
            :items="items.data"
            :dense="dense"
            :loading="loading"
            :server-items-length="items.total"
            loading-text="Loading... Please wait"
            :options.sync="options"
            :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
        >
          <template v-slot:item.action="{ item, index }">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                    @click="editItem(item.id)"
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
              <span>edit</span>
            </v-tooltip>
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                    @click="deleteItem(item.id)"
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
    <create-supplier v-if="dialog" :id="edit_id" v-model="dialog"></create-supplier>
  </v-container>
</template>
<script src="./js/index.js"></script>