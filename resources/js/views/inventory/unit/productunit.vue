<template>
  <v-container fluid>
    <v-card flat>
      <v-card-title>
        Product unit mapping, Total {{ items.total }}
        <v-spacer/>
        <v-text-field
            v-model="options.search"
            dusk="search_product"
            label="Search by product"
            single-line
            hide-details
            clearable
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
          <template v-slot:item.action="{ item,index }">

            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-icon
                    v-on="on"
                    small
                    class="mr-2"
                    color="success"
                    @click="editItem(item.id)"
                >
                  edit
                </v-icon>
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
    <v-dialog v-model="dialog" max-width="800px">
      <v-card>
        <v-card-title>
          <span class="headline">Product Unit Mapping</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <form @submit.prevent="postItem('forms')" data-vv-scope="forms">
              <v-row>
                <v-col cols="12" sm="12" md="12">
                  <v-autocomplete
                      v-model="forms.product_id"
                      label="Select Product"
                      :items="products"
                      item-text="name"
                      :loading="isLoading"
                      item-value="id"
                      name="product_id"
                      dusk="select_product"
                      v-validate="'required'"
                      :error-messages="errors.collect('forms.product_id')"
                      data-vv-name="product_id"
                      :search-input.sync="searchProduct"
                      return-object
                      dense
                      autocomplete="off"
                      no-filter
                  >
                    <template slot="item" slot-scope="{ item }">
                      <v-list-item-content>
                        <v-list-item-title>
                          {{ item.name }}
                        </v-list-item-title>
                        <v-list-item-action-text>
                          {{ item.code }}
                        </v-list-item-action-text>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
                </v-col>
                <v-col cols="12" sm="12" md="12">
                  <div id="select_unit">
                    <v-autocomplete
                        v-model="forms.unitList"
                        :items=units
                        name="unitList"
                        dusk="select_unit"
                        label="Select Unit"
                        item-text="key"
                        item-value="id"
                        v-validate="'required'"
                        :error-messages="errors.collect('forms.unitList')"
                        data-vv-name="unitList"
                        multiple
                        autocomplete="off"
                        chips
                    >
                    </v-autocomplete>
                  </div>
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
  </v-container>
</template>

<script src="./productunit.js"></script>
