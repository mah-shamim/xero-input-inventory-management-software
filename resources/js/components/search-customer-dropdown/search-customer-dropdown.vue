<template>
    <div class="search-customer-dropdown">
        <v-autocomplete :error-messages="errors.collect('forms.customer')"
                        :search-input.sync="searchCustomer"
                        placeholder="search customer"
                        v-validate="`${required}`"
                        v-model="forms.customer"
                        data-vv-name="customer"
                        :items="customers"
                        item-text="name"
                        label="Customer"
                        name="customer"
                        item-value="id"
                        return-object
                        outlined
        >
            <template v-slot:item="data">
                <template v-if="typeof data.item !== 'object'">
                    <v-list-item-content v-text="data.item"
                    ></v-list-item-content>
                </template>
                <template v-else>
                    <v-list-item-content>
                        <span v-html="data.item.name"></span>
                    </v-list-item-content>
                    <v-list-item-content>
                        <v-list-item-subtitle v-html="data.item.phone"
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
        props: ['forms', 'required'],
        data: () => ({
            customers: [],
            customer: null,
            searchCustomer: null
        }),
        watch: {
            forms:{
                handler(val){
                    if(val.hasOwnProperty('company_id') && !this.customers.length){
                        this.customers.push(val.customer)
                    }
                },
                immediate:true
            },
            searchCustomer: _.debounce(function (val) {
                this.getCustomers(val)
            }, 800)
        },
        methods: {
            getCustomers(query) {
                if (query) {
                    axios.get('/api/inventory/customers', {
                        params: {
                            lookup: query,
                            itemsPerPage: 10
                        }
                    })
                        .then(res => {
                            this.customers = res.data
                        })
                }
            }
        }
    };
</script>
