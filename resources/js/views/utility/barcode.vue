<template>
    <div class="container">
        <div class="row">
            <div class="col-4"> </div>
            <div class="col-4">
                <div class="form-element inline">
                    <select v-model="format">
                        <option value="CODE128A">CODE128A</option>
                        <option value="CODE128B">CODE128B</option>
                        <option value="CODE128C">CODE128C</option>
                        <option value="codabar">codabar</option>
                    </select>
                    <input type="text" v-model="barcodeValue" class="input-item">
                    <button @click="generateBarcode()" class="info s">Generate</button>
                </div>
                <div class="row">
                   <svg id="barcode" ></svg>
                </div>

                <div class="row">
                    <label >Print item</label>
                   <div class="form-element inline">
                       <input type="number" min="1" class="input-item" v-model="print_items">
                       <button type="button" class="btn warning s printBtn right"
                               @click="pos_print_barcode()">
                           Print With Pos
                       </button>
                       <button type="button" class="btn info s printBtn right"
                               @click="print_barcode()">
                           Print
                       </button>
                   </div>
                </div>


            </div>
            <div class="col-4"> </div>
        </div>
        <div class="row">
            <div class="col-12">

                <barcode-print
                        style="visibility: collapse"
                        :svgData="svgData"
                        :print_items="parseInt(print_items)"
                >

                </barcode-print>
                <barcode-print-for-pos
                        style="visibility: collapse"
                        :svgData="svgData"
                        :print_items="parseInt(print_items)"
                >

                </barcode-print-for-pos>
            </div>

        </div>
    </div>
</template>

<script>
    const JsBarcode = require('jsbarcode')
    const Canvas = require('canvas')
    export default {
        name: "barcode",
        props:['generateCode'],
        data(){return {
           barcodeValue:'1254er56464',
            format:'CODE128B',
            print_items:4,
            svgData:'asdfsdf'
        }},
        created(){

        },
        watch:{
            generateCode(){
                this.barcodeValue=this.generateCode?this.generateCode:123454678
            }
        },
        mounted(){
            if(this.$store.state.requestedBarcode){
                this.barcodeValue = this.$store.state.requestedBarcode
                window.setTimeout(()=>{
                    this.generateBarcode()
                }, 200)
            }
            if(!_.isEmpty(this.$route.query)){
                if(this.$route.query.hasOwnProperty('q')){
                    this.barcodeValue = this.$route.query.q
                    window.setTimeout(()=>{
                        this.generateBarcode()
                    }, 200)
                }
            }
            else{
                window.setTimeout(()=>{
                    JsBarcode('#barcode', '1254er56464',{
                        width:1,
                        height:40,
                    })
                }, 50)
            }

        },

        methods:{
            print_barcode(){
                this.svgData=document.getElementById('barcode').innerHTML
                setTimeout(()=>{
                    printJS({printable: 'barcode_print', type: 'html', showModal: false  })
                },1000)

            },
            pos_print_barcode(){
                this.svgData=document.getElementById('barcode').innerHTML
                setTimeout(()=>{
                    printJS({printable: 'barcode_pos_print', type: 'html', showModal: false  })
                },1000)
            },
            generateBarcode(){
                try {
                    JsBarcode('#barcode', this.barcodeValue, {
                        format:this.format,
                        width:1,
                        height:40,
                    })
                }catch (e) {
                    swal({
                        type: 'error',
                        timer: 5000,
                        text: e
                    })
                }
            }
        }

    }
</script>

<style lang="scss" scoped="">

</style>