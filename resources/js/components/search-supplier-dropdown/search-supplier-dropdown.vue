<template>
    <div class="search-supplier-dropdown">
        <v-autocomplete
            outlined
            name="supplier"
            item-value="id"
            label="Supplier"
            :items="suppliers"
            :loading="loading"
            item-text="company"
            :filter="filterObject"
            data-vv-name="supplier"
            v-model="forms.supplier_id"
            v-validate="`${required}`"
            placeholder="search supplier"
            :return-object="returnObject"
            :search-input.sync="searchSupplier"
            :error-messages="errors.collect('forms.supplier')"
        >
            <template v-slot:item="data">
                <template v-if="typeof data.item !== 'object'">
                    <v-list-item-content v-text="data.item" class="small"
                    ></v-list-item-content>
                </template>
                <template v-else>
                    <v-list-item-content class="small">
                        <span v-html="data.item.company"></span>
                    </v-list-item-content>
                    <v-spacer></v-spacer>
                    <v-list-item-content class="small">
                        <span v-html="data.item.name"></span>
                    </v-list-item-content>
                    <v-spacer></v-spacer>
                    <v-list-item-content>
                        <v-list-item-subtitle v-html="data.item.phone"
                                              class="small"
                        ></v-list-item-subtitle>
                    </v-list-item-content>
                </template>
            </template>
        </v-autocomplete>
    </div>
</template>

<script>
    import _ from 'lodash'

    export default {
        props: ['forms', 'required', 'returnObject'],
        data: () => ({
            suppliers: [],
            supplier: null,
            searchSupplier: null,
            loading: false
        }),
        watch: {
            forms: {
                handler(val) {
                    if (val.hasOwnProperty('company_id') && !this.suppliers.length) {
                        this.suppliers.push(val.supplier)
                    }
                },
                immediate: true
            },
            searchSupplier: _.debounce(function (val) {
                this.loading = true
                this.getSuppliers(val)
            }, 800)
        },
        methods: {
            filterObject(item, queryText, itemText){
                return item
            },
            getSuppliers(query) {
                if (query) {
                    axios.get('/api/inventory/get-suppliers', {
                        params: {
                            val:query
                        }
                    })
                        .then(res => {
                            this.suppliers = res.data
                            this.loading = false
                        })
                        .catch(error => {
                            this.loading = false
                        })
                }
            }
        }
    };
</script>
