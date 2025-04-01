<v-navigation-drawer
        v-model="drawer"
        {{--        :clipped="$vuetify.breakpoint.lgAndUp"--}}
        app
        {{--        :dark="_.isEmpty(settings.settings) && settings.settings.hasOwnProperty('design')?settings.settings.design.layout_color:null"--}}
        :style="{'backgroundColor':sideBarColor?sideBarColor:null }"
>
    <v-list :rounded="false" :dense="appDense">
        <v-list-item href="/home">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.home }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Home</v-list-item-title>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-group :prepend-icon="'mdi-account-check-outline'" no-action>
            <template v-slot:activator>
                <v-list-item-content>
                    <v-list-item-title>Product</v-list-item-title>
                </v-list-item-content>
            </template>
            <v-list-item :to="{name:'productsIndex'}">
                <v-list-item-title>Products</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{$root.icons.product}}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'purchase.index'}">
                <v-list-item-title>Purchases</v-list-item-title>
                <v-list-item-icon>
                    <v-icon color="primary">@{{ $root.icons.purchase }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'salesIndex'}">
                <v-list-item-title>Sales</v-list-item-title>
                <v-list-item-icon>
                    <v-icon color="success">@{{ $root.icons.sale }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'brandsIndex'}">
                <v-list-item-title>Brands</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.brand }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'unitsIndex'}">
                <v-list-item-title>Units</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.units }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'unitsMapping'}">
                <v-list-item-title>Unit mapping</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.unit_mapping }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'productUnitCreate'}">
                <v-list-item-title>Product Units</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.product_units }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'warrantyIndex'}">
                <v-list-item-title>Warranty</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.warranty }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'productdamagesIndex'}">
                <v-list-item-title>Damages</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.damages }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
        </v-list-group>
        <v-divider></v-divider>
        <v-list-item :to="{name:'expense.index'}">
            <v-list-item-icon>
                <v-icon>@{{$root.icons.expense}}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Expense</v-list-item-title>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item :to="{name:'customersIndex'}">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.customer }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Customers</v-list-item-title>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item :to="{name:'suppliersIndex'}">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.supplier }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Suppliers</v-list-item-title>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item :to="{name:'warehousesIndex'}">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.warehouse }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Warehouse</v-list-item-title>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item :to="{name:'categoriesIndex'}">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.category }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Category</v-list-item-title>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item :to="{name:'settings.index'}">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.setting }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Setting</v-list-item-title>
        </v-list-item>

    </v-list>
    <v-btn text small @click="appDense=!appDense">dense</v-btn>
</v-navigation-drawer>
