<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Brands, Total {{ items.total }}
        <v-spacer></v-spacer>
        <v-text-field
            v-model="options.search"
            :append-icon="$root.icons.search"
            label="Search"
            dusk="search"
            single-line
            hide-details
        ></v-text-field>
        <v-dialog v-model="dialog" max-width="800px">
          <template v-slot:activator="{ on }">
            <v-btn text dusk="open_brand_dialog" v-on="on" @click="forms={}">
              <v-icon>{{ $root.icons.create }}</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="headline">Brand</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <form @submit.prevent="postItem('forms')" data-vv-scope="forms">
                  <v-row>
                    <v-col cols="12" sm="12" md="12">
                      <v-text-field
                          label="name*"
                          hint="mdit, cocacola, 7up"
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
                    <v-col cols="12" sm="12" md="12">
                      <v-textarea
                          label="description"
                          persistent-hint
                          name="description"
                          dusk="description"
                          v-model="forms.description"
                          data-vv-name="description"
                          :error-messages="errors.collect('forms.description')"
                      ></v-textarea>
                    </v-col>

                    <v-card-actions justify="flex-end">
                      <v-btn color="blue darken-1" text @click="dialog = false">
                        Close
                      </v-btn>
                      <v-btn color="success darken-1" text type="submit" dusk="submit" success>
                        Save
                      </v-btn>
                    </v-card-actions>
                  </v-row>
                </form>
              </v-container>
              <small>*indicates required field</small>
            </v-card-text>
          </v-card>
        </v-dialog>
        <v-btn text @click="dense=!dense">
          <v-icon>mdi-arrow-collapse-vertical</v-icon>
        </v-btn>
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
  </v-container>
</template>
<script src="./js/index.js"></script>