<template>
    <div class="container">
        <div class="row">
            <div class="col-1">
                <form @submit.prevent="importProduct()" @keydown="forms.errors.clear($event.target.name)" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-1">
                            <div class="form-element inline">
                                <label>import file</label>
                                <input type="file" class="input-item" accept=".xls, .xlsx, .csv" id="file" ref="file" @change="handleFileUpload"/>

                                <label>select unit</label>
                                <select v-model="forms.unit" class="input-item">
                                    <option v-for="unit in units" :key="unit.id" :value="unit.id">{{unit.key}}</option>
                                </select>

                                <button type="submit" class="info s">submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "import",
        data(){
            return {
                forms: this.$root.$data.forms,
                units:[]
            }
        },
        created(){
            axios.get('/api/inventory/units?dropdown=true').then(res=>{
                this.units = res.data
            })
        },
        methods:{
            handleFileUpload(){
                this.forms.file = this.$refs.file.files[0]
            },
            importProduct(){
                let formData = new FormData()
                formData.append('file', this.forms.file)
                axios.post('/api/inventory/products/import?base_unit_id='+this.forms.unit, formData,{headers:{'Content-Type': 'multipart/form-data'}}).then(res=>{
                    console.log(res.data)
                })
            }
        }

    }
</script>

<style scoped>

</style>