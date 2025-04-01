<template>
    <div id="barcode_print" v-if="isLoaded">

        <div class="printContent">
            <svg v-html="svgData" v-for="item in parseInt(print_items)" width="15%">

            </svg>
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