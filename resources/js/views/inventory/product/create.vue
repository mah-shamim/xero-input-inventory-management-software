<template>
  <v-dialog width="1000" v-model="showModal" id="product_create">
    <v-card>
      <v-card-title>{{ `${editId ? 'Update ' : 'Create '}` }}Product</v-card-title>
      <form @submit.prevent="postForm('forms')" data-vv-scope="forms">
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="8">
                <v-row>
                  <v-col cols="12" md="4">
                    <v-text-field
                        name="name"
                        dusk="name"
                        label="Product name"
                        v-model="forms.name"
                        v-validate="'required'"
                        :error-messages="errors.collect('forms.name')"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                        name="code"
                        dusk="code"
                        label="Code"
                        v-model="forms.code"
                        v-validate="'required'"
                        :error-messages="errors.collect('forms.code')"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                        name="slug"
                        dusk="slug"
                        slug="slug"
                        label="Slug"
                        v-model="forms.slug"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                        step="any"
                        type="number"
                        name="buying_price"
                        dusk="buying_price"
                        label="buying Price"
                        v-validate="'required'"
                        data-vv-as="buying price"
                        v-model="forms.buying_price"
                        :error-messages="errors.collect('forms.buying_price')"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                        step="any"
                        name="price"
                        dusk="price"
                        type="number"
                        v-model="forms.price"
                        label="Selling Price"
                        v-validate="'required'"
                        data-vv-as="price"
                        :error-messages="errors.collect('forms.price')"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-autocomplete
                        :items="units"
                        item-text="key"
                        item-value="id"
                        label="Base Unit"
                        name="base_unit_id"
                        dusk="base_unit_id"
                        data-vv-as="base unit"
                        v-validate="'required'"
                        v-model="forms.base_unit_id"
                        :error-messages="errors.collect('forms.base_unit_id')"
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-autocomplete
                        label="Brand"
                        :items="brands"
                        name="brand_id"
                        dusk="brand_id"
                        item-value="id"
                        item-text="name"
                        data-vv-as="brand"
                        v-validate="'required'"
                        v-model="forms.brand_id"
                        :error-messages="errors.collect('forms.brand_id')"
                    ></v-autocomplete>
                  </v-col>
                </v-row>
              </v-col>
              <v-col cols="12" md="4">
                <p class="error--text"  v-if="!_.isEmpty(errors.collect('forms.categories'))">
                  {{ errors.collect('forms.categories')[0] }}
                </p>
                <v-text-field
                    hide-details
                    v-model="search"
                    label="Search Category"
                ></v-text-field>
                <v-treeview
                    dense
                    rounded
                    hoverable
                    selectable
                    return-object
                    :filter="filter"
                    :search="search"
                    :items="categories"
                    v-model="forms.category_list"
                    :error-messages="errors.collect('forms.categories')"
                ></v-treeview>
              </v-col>
            </v-row>

          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn
              small
              success
              type="submit"
              color="primary"
              dusk="submit_new"
              @click="submit_button='new'"
              v-if="!editId"
          >
            Submit & New
          </v-btn>
          <v-btn
              small
              type="submit"
              color="success"
              dusk="submit_close"
              @click="submit_button='close'"
          >
            Submit & Close
          </v-btn>
          <v-btn small @click="forms={}" v-if="!editId">reset</v-btn>
        </v-card-actions>
      </form>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: {
    value: {
      type: Boolean,
      required: true
    },
    editId:{
      type:Number,
      default:null
    }
  },
  data: () => ({
    forms: {},
    units: [],
    brands: [],
    search: null,
    dialog: false,
    categories: [],
    caseSensitive: false,
    submit_button: ''
  }),
  watch: {
    editId:{
      immediate:true,
      handler(val){
        this.forms.id=val
      }
    }
  },
  created() {
    this.forms.categories = []
    this.getData()
  },
  computed: {
    filter() {
      return this.caseSensitive
          ? (item, search, textKey) => item[textKey].indexOf(search) > -1
          : undefined
    },
    showModal: {
      get() {
        return this.value;
      },
      set(value) {
        if (!value) this.$emit('input', value)
      }
    }
  },
  methods: {
    getData:_.debounce(function (){
      if(!this.editId){
        axios.get('/api/inventory/products/create?dropdown=true&withJsonFormat=true')
            .then(res => {
              this.units = res.data.units
              this.brands = res.data.brands
              this.categories = res.data.categories
            })
      }else{
        axios.get(`/api/inventory/products/${this.editId}/edit`)
            .then(res=>{
              this.brands = res.data.brands
              this.units = res.data.units
              this.categories = res.data.categories
              let items = ['code', 'name', 'slug', 'buying_price', 'price', 'base_unit_id', 'brand_id']
              items.forEach(i=>this.forms[i] = res.data.products[i])
              this.forms.category_list = res.data.products.categories
            })
      }
    }),
    postForm(scope) {
      this.$validator.validateAll(scope)
          .then(result => {
            if (result) {
              this.saveItem(scope)
            }
          })
    },
    saveItem(scope) {
      let url = `/api/inventory/products`
      let method = 'post'
      this.forms.categories = _.map(this.forms.category_list, 'id')
      if(this.editId){
        url = `/api/inventory/products/${this.editId}`
        method = 'patch'
      }
      axios[method](url, this.forms)
          .then(res => {
            swal({
              type: res.data.type,
              timer: 2000,
              text: res.data.message,
              showCancelButton: false,
              showConfirmButton: false,
            }).catch(swal.noop)
            if (this.submit_button === 'new') {
              this.forms = {}
            }
            if (this.submit_button === 'close') {
              this.showModal = false
            }
          })
          .catch(error => {
            let err
            let errs = error.response.data.errors
            for (err in errs) {
              this.errors.add({
                'field': err,
                'msg': errs[err][0],
                scope: scope
              })
            }
          })
    }
  }
}
</script>
