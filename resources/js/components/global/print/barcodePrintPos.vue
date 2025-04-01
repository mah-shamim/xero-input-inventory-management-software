<template>
    <div id="barcode_pos_print" v-if="isLoaded" class="pos-print">
        <div class="printContent">
            <template v-for="item in parseInt(print_items)" width="20%">
                <svg v-html="svgData" >

                </svg><br>
            </template>

        </div>



    </div>

</template>

<script>
    export default {

        props: {
            svgData:{
                type:String,
                default:()=>''
            },
            print_items:{
                type:Number,
                default:()=>0
            }
        },
        name: "barcodePrint",

        computed: {
            isLoaded() {
                return !_.isEmpty(this.svgData)
            },


        },
        watch: {
            svgData() {
                let result = this.svgData.match( /<rect /i );
                let result2 = this.svgData.match( /<g/i );
                let sliceData = this.svgData.slice(result.index, result2.index)
                this.svgData=this.svgData.replace(sliceData,'')
            }
        },


    }
</script>

<style scoped>
    .pos-print {
        width: 400px /* normal width */
    }
    @media print {
        .pos-print {
            width: 100% /* print width */
        }
    }
</style>
