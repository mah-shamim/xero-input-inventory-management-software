<v-app-bar
    app
    :dense="appDense"
    :style="{'backgroundColor':topBarColor?topBarColor:null}"
>
    <v-toolbar-title
            style="width: 300px"
            class="ml-0 pl-3"
    >
        <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-btn href="/home" text>
            <span class="hidden-sm-and-down">@{{ $store.state.settings.settings.site_name }}</span>
        </v-btn>

    </v-toolbar-title>
    {{--<v-text-field--}}
    {{--flat--}}
    {{--solo-inverted--}}
    {{--hide-details--}}
    {{--prepend-inner-icon="search"--}}
    {{--label="Search"--}}
    {{--class="hidden-sm-and-down"--}}
    {{--></v-text-field>--}}
    <v-spacer></v-spacer>
    <v-btn :to="{name:'purchase.index'}" icon>
        <v-icon color="primary">@{{ $root.icons.purchase }}</v-icon>
    </v-btn>
    <v-btn :to="{name:'salesIndex'}" icon>
        <v-icon color="success">@{{ $root.icons.sale }}</v-icon>
    </v-btn>
    <v-btn icon>
        <v-icon>mdi-bell</v-icon>
    </v-btn>
    <v-menu
            left
            bottom
    >
        <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
                <v-avatar>
                    <v-icon>mdi-account-circle</v-icon>
                </v-avatar>
            </v-btn>
        </template>

        <v-list>
            <v-list-item>
                <v-list-item-title>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </v-list-item-title>
            </v-list-item>
        </v-list>
    </v-menu>
</v-app-bar>
