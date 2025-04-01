<template>
    <v-container>
        <v-card :loading="loading">
            <v-card-title>Product create</v-card-title>
            <form @submit.prevent="postForm('forms')" data-vv-scope="forms">
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    name="name"
                                    label="Product name"
                                    v-model="forms.name"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('forms.name')"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    name="code"
                                    label="Code"
                                    v-model="forms.code"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('forms.code')"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    name="slug"
                                    label="Slug"
                                    v-model="forms.slug"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    type="number"
                                    name="buying_price"
                                    label="buying Price"
                                    v-validate="'required'"
                                    data-vv-as="buying price"
                                    v-model="forms.buying_price"
                                    :error-messages="errors.collect('forms.buying_price')"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    name="price"
                                    type="number"
                                    v-model="forms.price"
                                    label="Selling Price"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('forms.price')"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-text-field
                                    name="measurement"
                                    label="Measurement in cm"
                                    v-model="forms.measurement"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="4">
                                <div class="d-flex">
                                    <v-select
                                        multiple
                                        return-object
                                        item-value="id"
                                        label="Category"
                                        item-text="name"
                                        name="category_id"
                                        data-vv-as="category"
                                        v-validate="'required'"
                                        :items="forms.categories"
                                        v-model="forms.category_id"
                                        :error-messages="errors.collect('forms.category_id')"
                                    ></v-select>
                                    <v-dialog
                                        width="500"
                                        v-model="dialog"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-btn text icon color="indigo" v-on="on">
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </template>
                                        <v-card>
                                            <v-card-title>Select Category</v-card-title>
                                            <v-card-text>
                                                <v-container>
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
                                                        v-model="forms.categories"
                                                    ></v-treeview>
                                                </v-container>
                                            </v-card-text>
                                        </v-card>
                                    </v-dialog>
                                </div>
                            </v-col>
                            <v-col cols="12" md="4">
                                <v-autocomplete
                                    :items="units"
                                    item-text="key"
                                    item-value="id"
                                    label="Base Unit"
                                    name="base_unit_id"
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
                                    item-value="id"
                                    item-text="name"
                                    data-vv-as="brand"
                                    v-validate="'required'"
                                    v-model="forms.brand_id"
                                    :error-messages="errors.collect('forms.brand_id')"
                                ></v-autocomplete>
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
                        @click="submit_button='new'"
                    >
                        submit & new
                    </v-btn>
                    <v-btn
                        small
                        type="submit"
                        color="success"
                        @click="submit_button='view'"
                    >
                        submit & view
                    </v-btn>
                    <v-btn
                        small
                        @click="forms={}"
                    >
                        reset
                    </v-btn>
                </v-card-actions>
            </form>
        </v-card>
    </v-container>
</template>
<script>
    export default {
        data: () => ({
            forms: {},
            units: [],
            brands: [],
            search: null,
            dialog: false,
            categories: [],
            submit_button: '',
            caseSensitive: false,
            loading: true
        }),
        computed: {
            filter() {
                return this.caseSensitive
                    ? (item, search, textKey) => item[textKey].indexOf(search) > -1
                    : undefined
            },
        },
        watch: {
            'forms.categories': {
                deep: true,
                handler(val) {
                    this.forms.category_id = []
                    this.forms.category_id = val
                }
            }
        },
        created() {
            this.loading = true
            if (this.$route.params && this.$route.params.hasOwnProperty('id')) {
                let id = this.$route.params.id
                this.getProduct(id)
            }
        },
        methods: {
            postForm(scope) {
                this.$validator.validateAll(scope)
                    .then(result => {
                        if (result) {
                            let id = this.$route.params.id
                            this.loading = true
                            axios.patch('/api/inventory/products/' + id, this.forms)
                                .then(res => {
                                    swal({
                                        type: res.data.type,
                                        timer: 2000,
                                        text: res.data.message,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                    }).catch(swal.noop)
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
                                .finally(() => {
                                    this.loading = false
                                    this.forms = {}
                                    if (this.submit_button === 'view') {
                                        this.$router.push({name: 'productsShow', params: {id: id}})
                                    }
                                    if (this.submit_button === 'new') {
                                        this.$router.push({name: 'productsCreate'})
                                    }
                                })
                        }
                    })
            },
            getProduct(id) {
                axios.get('/api/inventory/products/' + id + '/edit')
                    .then(res => {
                        this.loading = false
                        this.units = res.data.units
                        this.brands = res.data.brands
                        this.forms = res.data.products
                        this.categories = res.data.categories
                    })
            }
        }
    }
</script>
