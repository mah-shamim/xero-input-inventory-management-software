<template>
  <v-container fluid>
    <v-card flat>
      <v-layout>
        <v-col cols="6" sm="6" md="6">
          <v-sheet>
            <v-text-field
                v-model="search"
                label="Search Category Directory"
                hide-details
                clearable
                clear-icon="mdi-close-circle-outline"
            ></v-text-field>
          </v-sheet>
          <v-card-text>
            <v-treeview
                :active.sync="active"
                :items="items"
                :search="search"
                :filter="filter"
                :open.sync="open"
                activatable
                transition
                :load-children="getItemsList"
            >
              <template v-slot:prepend="{ item, active }">
                <v-icon
                    v-if="item.children"
                    v-text="`mdi-${item.id === 1 ? 'home-variant' : 'folder-network'}`"
                ></v-icon>
              </template>
            </v-treeview>
          </v-card-text>
        </v-col>
        <v-divider vertical></v-divider>
        <v-col cols="6" sm="6" md="6">
          <v-btn color="primary" @click="forms={}, active=[]" small>
            <v-icon>mdi-plus</v-icon>
          </v-btn>
          <v-btn class="align-right" small color="error" v-if="forms.id" @click="deleteItem()" dusk="delete">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
          <form @submit.prevent="postItem('forms')" id="create_category" data-vv-scope="forms">
            <v-row>
              <v-col cols="12" sm="12" md="12">
                <v-text-field
                    label="name"
                    hint="mdit, cocacola, 7up"
                    name="name"
                    dusk="name"
                    v-model="forms.name"
                    v-validate="'required'"
                    :error-messages="errors.collect('forms.name')"
                    data-vv-name="name"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="12" md="12">
                <v-select
                    v-model="forms.type"
                    :items=types
                    label="Item"
                    name="type"
                    dusk="type"
                    v-validate="'required'"
                    :error-messages="errors.collect('forms.type')"
                    data-vv-name="type"
                ></v-select>
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
              <v-col cols="12" sm="12" md="12">
                <v-autocomplete
                    v-model="forms.parent_id"
                    :items=parents
                    label="Parent"
                    dusk="parent"
                    item-text="name"
                    item-value="id"
                    clearable
                ></v-autocomplete>
              </v-col>
              <v-card-actions justify="flex-end">
                <v-btn color="success darken-1" type="submit" dusk="submit" success>
                  Save
                </v-btn>
              </v-card-actions>
            </v-row>
          </form>
          <v-divider horizontal></v-divider>
          <v-layout v-if="forms.hasOwnProperty('id')">
            <v-col>
              <p>
                Name: {{ forms.name }}
              </p>
              <p>description:{{ forms.description }}</p>
              <p>type: {{ forms.type }}</p>
            </v-col>
          </v-layout>
        </v-col>
      </v-layout>
    </v-card>
  </v-container>
</template>

<script src="./js/index.js"></script>