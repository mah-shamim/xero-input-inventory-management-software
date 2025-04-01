<template>
    <div class="warehouse-list-dropdown">
        <v-autocomplete
            cache-items
            item-value="id"
            name="warehouse"
            item-text="name"
            label="warehouse"
            :loading="loading"
            :items="warehouses"
            v-model="warehouse"
            @change="updateWarehouse"
            :search-input.sync="search"
            :return-object="returnObject"
            clearable
        ></v-autocomplete>
    </div>
</template>

<script>
    export default {
        props: {
            returnObject: false
        },
        data: () => ({
            warehouse: null,
            warehouses: [],
            search: null,
            loading: false
        }),
        watch: {
            search(val) {
                this.loading = true
                if (val) {
                    this.getData(val)
                }else{
                    this.loading=false
                }
            }
        },
        methods: {
            getData: _.debounce(function (val) {
                axios.get('/api/inventory/warehouses?dropdown=true', {
                    params: {name_code: val}
                })
                    .then(res => {
                        this.warehouses = res.data
                        this.loading = false
                    })
                    .catch(error => {
                        this.loading = false
                    })
            }, 400),
            updateWarehouse() {
                this.$emit('input', this.warehouse)
            }
        }
    };
</script>
