<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Unit Conversion, Total {{ items.total }}
        <v-spacer/>
        <v-text-field
            v-model="options.search"
            label="Search"
            single-line
            hide-details
        ></v-text-field>
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
            :items="items.data"
            :loading="loading"
            :server-items-length="items.total"
            loading-text="Loading... Please wait"
            :options.sync="options"
            :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
        >
          <template v-slot:item.to_unit="{item}">
            {{ item.to_unit.key }}
          </template>
          <template v-slot:item.from_unit="{item}">
            {{ item.from_unit.key }}
          </template>
          <template v-slot:item.conversion_factor="{item}">
            {{ item.conversion_factor | toFix(2) }}
          </template>
          <template v-slot:item.action="{ item, index }">

            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                    @click="edit_id=item.id"
                    :dusk="`edit-${index}`"
                    small
                    v-on="on"
                    fab
                    text
                    color="success"
                >
                  <v-icon small>{{ $root.icons.edit }}</v-icon>
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
                  <v-icon small>{{ $root.icons.delete }}</v-icon>
                </v-btn>
              </template>
              <span>Delete</span>
            </v-tooltip>
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
    <create-mapping v-model="dialog" :model_id="edit_id" v-if="dialog"/>
  </v-container>
</template>
<script src="./mapping.js"></script>
