<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Product Damage, Total {{ items.total }}, Sale Value Total: {{ sale_value_total }}
        <v-spacer/>
        <v-text-field
            single-line
            hide-details
            label="Search"
            dusk="search"
            v-model="options.search"
        ></v-text-field>
        <action-btn
            text="CReate"
            dusk="create"
            :icon="$root.icons.create"
            @click="dialog=true"
        />
        <collapse-btn @click="dense=!dense"/>
      </v-card-title>
      <v-card-text>
        <v-data-table
            :dense="dense"
            :headers="headers"
            :loading="loading"
            :items="items.data"
            :options.sync="options"
            :server-items-length="items.total"
            loading-text="Loading... Please wait"
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
    <template v-if="dialog">
      <add-product-damage
          v-model="dialog"
          :id="id"
      />
    </template>
  </v-container>
</template>
<script src="./js/index.js"></script>
