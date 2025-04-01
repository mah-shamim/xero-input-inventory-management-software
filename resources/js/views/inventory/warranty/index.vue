<template>
  <v-container fluid>
    <template v-if="dialog">
      <add-warranty
          v-model="dialog"
          :id="id"
      />
    </template>
    <v-card flat>
      <v-card-title>
        Warranty, Total {{ items.total }}
        <v-spacer/>
        <v-text-field
            clearable
            class="mr-2"
            single-line
            hide-details
            label="Product Name"
            name="search_product_name"
            dusk="search_product_name"
            v-model="options.product_name"
        />
        <v-text-field
            clearable
            single-line
            hide-details
            label="Customer Name"
            name="search_customer_name"
            dusk="search_customer_name"
            v-model="options.customer_name"
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
  </v-container>
</template>
<script src="./js/index.js"></script>
