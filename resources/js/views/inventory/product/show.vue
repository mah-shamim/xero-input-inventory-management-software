<template>
    <v-container v-if="product">
        <v-card :loading="loading">
            <v-card-title>{{product.name}} &nbsp;<v-btn @click="redirectEdit()" small>
              <v-icon small v-text="$root.icons.edit"/>
            </v-btn>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <p>Code: {{product.code}}</p>
                    <p>Purchase Price: {{product.buying_price}}</p>
                    <p>Sale Price: {{product.price}}</p>
                    <p>Brand Name: {{product.brands.name}}</p>
                    <p>Categories: <span v-for="cat in product.categories">{{cat.name}},&nbsp;</span></p>
                    <p>Base Unit: {{product.unit.key}}</p>
                    <p>Unit Mapped: <span v-for="unit in product.units">{{unit.key}},&nbsp;</span></p>
                </v-container>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
    export default {
        name: "show",
        data: () => ({
            product: null,
            loading: true
        }),
        created() {
            this.loading = true
            if (!_.isEmpty(this.$route.params) && this.$route.params.hasOwnProperty('id')) {
                let id = this.$route.params.id
                this.getProduct(id)
            }
        },
        methods: {
            redirectEdit() {
                this.$router.push({name: 'productsEdit', params: {id: this.$route.params.id}})
            },
            getProduct(id) {
                axios.get('/api/inventory/products/' + id)
                    .then(res => {
                        this.product = res.data.product
                        this.loading = false
                    })
            }
        }
    }
</script>

<style scoped>

</style>
