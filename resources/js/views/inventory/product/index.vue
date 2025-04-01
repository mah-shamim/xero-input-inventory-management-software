<template>
  <v-container fluid>
    <v-card id="filter_panel" flat v-if="showFilter">
      <v-card-title>
        Filter
        <v-btn
            text
            outlined
            small
            dark
            class="ml-2"
            color="green"
            dusk="create_dialog"
            @click="showFilter=!showFilter"
        >
          Filter
          <v-icon v-if="showFilter">
            {{ $root.icons.filterOff }}
          </v-icon>
          <v-icon v-else>
            {{ $root.icons.filter }}
          </v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <v-col>
            <v-text-field
                dense
                clearable
                name="name_code_search"
                dusk="name_code_search"
                label="Search by name, or code"
                v-model="options.name_code_search"
            />
          </v-col>
          <v-col>
            <v-autocomplete
                dense
                clearable
                name="brand"
                item-value="id"
                :items="brands"
                item-text="name"
                dusk="brand_search"
                label="Search Brand"
                v-model="options.brand_id"
            />
          </v-col>
          <v-col>
            <v-autocomplete
                dense
                clearable
                name="category"
                item-value="id"
                :items="categories"
                item-text="name"
                dusk="category_search"
                label="Search Category"
                v-model="options.categories"
            />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    <v-card flat>
      <v-card-title>
        Products
        <v-spacer></v-spacer>
        <v-btn
            text
            outlined
            small
            class="mr-2"
            color="green"
            dusk="filter_open_close"
            @click="showFilter=!showFilter"
        >
          Filter
          <v-icon v-html="`${!showFilter?$root.icons.filter:$root.icons.filterOff}`">
          </v-icon>
        </v-btn>
        <action-btn
            text="Create"
            dusk="create"
            @click="warehouseCreateDialog=true"
        ></action-btn>
      </v-card-title>
      <v-card-text>
        <v-data-table
            :dense="dense"
            :loading="loading"
            :headers="headers"
            :items="items.data"
            :options.sync="options"
            :server-items-length="items.total"
            loading-text="Loading... Please wait"
            :footer-props="{itemsPerPageOptions: $store.state.itemsPerPageOptions}"
        >
          <template v-slot:item.brand="{item}">
            {{ item.brands.name }}
          </template>
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

    <!--    custom component-->
    <ProductCreate
        v-model="warehouseCreateDialog"
        :edit-id="edit_id"
        v-if="warehouseCreateDialog"
    />
  </v-container>
</template>
<script src="./index.js"></script>
