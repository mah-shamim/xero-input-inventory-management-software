<v-navigation-drawer
        v-model="drawer"
        {{--        :clipped="$vuetify.breakpoint.lgAndUp"--}}
        app
        {{--    :dark="settings.settings.hasOwnProperty('design')?settings.settings.design.layout_color:null"--}}
        {{--    :style="{'backgroundColor':settings.settings.hasOwnProperty('design')?settings.settings.design.sidebar_color:null }"--}}
        :style="{'backgroundColor':sideBarColor?sideBarColor:null }"
>
    <v-list :dense="appDense">
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
                    <v-list-item-title>Inventory</v-list-item-title>
                </v-list-item-content>
            </template>
            <v-divider></v-divider>
            <v-list-item :to="{name:'purchaseReportIndex'}">
                <v-list-item-title>Purchase</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.purchase }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'saleReportIndex'}">
                <v-list-item-title>Sales</v-list-item-title>
                <v-list-item-icon>
                    <v-icon color="red">@{{ $root.icons.sale }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'customerReportIndex'}">
                <v-list-item-title>Customer</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.customer }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'supplierReportIndex'}">
                <v-list-item-title>Supplier</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.supplier }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'productReportIndex'}">
                <v-list-item-title>Product</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.product }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
            <v-list-item :to="{name:'warehouseReportIndex'}">
                <v-list-item-title>Warehouse</v-list-item-title>
                <v-list-item-icon>
                    <v-icon>@{{ $root.icons.warehouse }}</v-icon>
                </v-list-item-icon>
            </v-list-item>
        </v-list-group>
        <v-list-item :to="{name:'report.expense.index'}">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.expense }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Expense</v-list-item-title>
        </v-list-item>
        <v-list-item :to="{name:'report.overall'}">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.calculator }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Overall</v-list-item-title>
        </v-list-item>
    </v-list>
    <template v-slot:append>
        <div>
            <v-btn text small @click="appDense=!appDense">dense</v-btn>
            <v-btn text small>Logout</v-btn>
        </div>
    </template>
</v-navigation-drawer>

