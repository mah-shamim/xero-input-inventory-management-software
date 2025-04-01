<v-navigation-drawer
        v-model="drawer"
        {{--        :clipped="$vuetify.breakpoint.lgAndUp"--}}
        app
        {{--    :dark="settings.settings.hasOwnProperty('design')?settings.settings.design.layout_color:null"--}}
        {{--    :style="{'backgroundColor':settings.settings.hasOwnProperty('design')?settings.settings.design.sidebar_color:null }"--}}
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
        <v-list-item :to="{name:'payroll.department'}">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.homeGroup }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>
                Department
            </v-list-item-title>
        </v-list-item>
        <v-list-item :to="{name:'payroll.employee'}">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.accountHat }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>
                Employee
            </v-list-item-title>
        </v-list-item>
        <v-list-item :to="{name:'payroll.salary'}">
            <v-list-item-icon>
                <v-icon>@{{ $root.icons.payment }}</v-icon>
            </v-list-item-icon>
            <v-list-item-title>
                Salary
            </v-list-item-title>
        </v-list-item>
    </v-list>


    <v-btn text small @click="appDense=!appDense">dense</v-btn>
</v-navigation-drawer>
