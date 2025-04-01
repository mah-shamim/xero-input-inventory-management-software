<template>
    <div class="category-list-dropdown">
        <v-autocomplete
            cache-items
            item-value="id"
            name="category"
            item-text="name"
            label="category"
            :loading="loading"
            :items="categories"
            v-model="category"
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
            returnObject: false,
            types:{
                type:String,
                default:'PRODUCT'
            }
        },
        data: () => ({
            category: null,
            categories: [],
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
                axios.get('/api/inventory/categories?dropdown=true', {
                    params: {
                        name: val,
                        type:this.types
                    }
                })
                    .then(res => {
                        this.categories = res.data
                        this.loading = false
                    })
                    .catch(error => {
                        this.loading = false
                    })
            }, 400),
            updateWarehouse() {
                this.$emit('input', this.category)
            }
        }
    };
</script>
