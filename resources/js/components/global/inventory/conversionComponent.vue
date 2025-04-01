<template>
    <div>
        <v-dialog
            v-model="dialog"
            width="500"
        >
            <template v-slot:activator="{ on }">
                <v-btn
                    icon
                    outlined
                    dark
                    x-small
                    v-on="on"
                    color="green lighten-2"
                >
                    <v-icon>{{$root.icons.convert}}</v-icon>
                </v-btn>
            </template>
            <v-card>
                <v-card-text class="pt-5">
                    <v-select
                        solo
                        :items="units"
                        item-value="id"
                        item-text="key"
                        label="unit"
                        v-model="baseUnitData"
                        @input="showConversion()"
                    ></v-select>
                    <v-text-field
                        solo
                        step="any"
                        type="number"
                        label="quantity"
                        v-model="quantityData"
                        @keyup="showConversion"
                    ></v-text-field>
                    <v-select
                        solo
                        :items="units"
                        item-value="id"
                        item-text="key"
                        label="unit"
                        v-model="toUnitData"
                        @input="showConversion"
                    ></v-select>
                    <v-text-field
                        solo
                        disabled
                        step="any"
                        type="number"
                        label="quantity"
                        v-model="conQuantity"
                        @keyup="showConversion"
                    ></v-text-field>
                    <p v-text="conversionStr"></p>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    export default {
        props: {
            baseUnit: {
                type: Number,
                default: () => 0
            },
            toUnit: {
                type: Number,
                default: () => 0
            },
            quantity: {
                type: Number,
                default: () => 0
            },
            units: {
                type: Array,
                default: () => []
            },
            componentId: {
                type: String,
                default: () => "conversion"
            },
            isPurchase: {
                type: Boolean,
                default: () => false
            },
            productId: {
                type: Number,
                default: () => 0
            }
        },
        data() {
            return {
                conQuantity: 0,
                baseUnitData: 0.0,
                toUnitData: 0,
                quantityData: 0,
                conversionStr: "",
                dialog: false
            }
        },
        watch: {
            baseUnit(val) {
                this.baseUnitData = val
            },
            toUnit(val) {
                this.toUnitData = val
            },
            quantity(val) {
                this.quantityData = val
            }
        },
        created() {

            this.baseUnitData = this.baseUnit
            this.toUnitData = this.toUnit
            this.quantityData = this.quantity
        },
        methods: {
            showConversion() {
                if (this.baseUnitData == 0 || this.toUnitData == 0 || this.quantityData == 0 || isNaN(this.quantityData)) {
                    return
                }
                let url = '/api/inventory/unitconversions/' + this.baseUnitData + '/' + this.toUnitData + '/' + this.quantityData
                axios.post(url, {isPurchase: this.isPurchase, productId: this.productId})
                     .then(response => {
                         this.conQuantity = response.data.quantity
                         this.conversionStr = response.data.conversionStr
                     })
            },
            showModal() {
                this.showConversion()
            }
        }
    }
</script>

<style scoped>

</style>
