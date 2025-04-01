<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>
<body>
<div id="app">
    <v-app>
        <v-main>
            <v-container fill-height class="justify-center">
                <a href="/inventory/sales" text>
                    <v-card
                        class="mx-auto"
                        max-width="200"
                        min-width="200"
                    >
                        <v-img
                            class="white--text align-end"
                            height="100px"
                            src="/image/inventory.jpeg"
                        >
                            <v-card-title>INVENTORY</v-card-title>
                        </v-img>
                    </v-card>
                </a>
                <a href="/payroll/employee">
                    <v-card
                        class="mx-auto"
                        max-width="200"
                        min-width="200"
                    >
                        <v-img
                            class="white--text align-end"
                            height="100px"
                            src="/image/payroll.jpeg"
                        >
                            <v-card-title>PAYROLL</v-card-title>
                        </v-img>
                    </v-card>
                </a>
                <a href="/report/overall">
                    <v-card
                        class="mx-auto"
                        max-width="200"
                        min-width="200"
                    >
                        <v-img
                            class="white--text align-end"
                            height="100px"
                            src="/image/report.jpeg"
                        >
                            <v-card-title>REPORT</v-card-title>
                        </v-img>
                    </v-card>
                </a>
            </v-container>
        </v-main>
    </v-app>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
<script>
    new Vue({
        el: '#app',
        vuetify: new Vuetify(),
    })
</script>
</body>
</html>
