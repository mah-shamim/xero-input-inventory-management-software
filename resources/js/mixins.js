export default {
    methods: {
        $axiosPostDialog(data) {
            swal.fire({
                icon: data.type,
                timer: 2000,
                text: data.message,
                showCancelButton: false,
                showConfirmButton: false,
            })
                .catch(swal.noop)
        },
        async $deleteWithConfirmation(obj) {
            let returnData = false
            await swal.fire({
                type: "warning",
                text: obj.text,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: 'Yes, delete it!'
            })
                .then(async (result) => {
                    if (result.value) {
                        await axios.delete(obj.url)
                            .then(res => {
                                swal.fire({
                                    type: res.data.type,
                                    timer: 3000,
                                    text: res.data.message,
                                    showCancelButton: false,
                                    showConfirmButton: false
                                })
                                returnData = result.value
                            })
                    }
                })
            return new Promise((res, rej) => {
                if (returnData) {
                    res(returnData)
                } else {
                    rej(returnData)
                }
            })
        },
        $getAxiosMethod(is_dialog, model_id) {
            return model_id || this.$route.params.id ? 'patch' : 'post';
        },
        $getUrl(model_name, is_dialog, model_id, module_name = null) {

            let base_url = api_base_url + '/' + model_name

            if (module_name) {
                base_url = '/api/' + module_name + '/' + model_name
            }

            let url = base_url + '/create'

            if (is_dialog && model_id) {
                // console.log(1)
                url = base_url + '/' + this.modelId + '/edit'
            }

            if (!is_dialog && this.$route.params.id) {
                // console.log(2, model_id, is_dialog)
                url = base_url + '/' + this.$route.params.id + '/edit'
            }
            return url
        },
        $getUrlForm(url) {
            return url.replace(/edit|create/g, '').replace(/\/$/, "")
        },
        $setPickingLocation(warehouse_id, picking_id) {
            return `${auth.user.company_id}-${warehouse_id}-${picking_id}`
        },
        $setStorageLocation(warehouse_id, isle_id, rack_id, bin_id) {
            return `${auth.user.company_id}-${warehouse_id}-${isle_id}-${rack_id}-${bin_id}`
        },


        // return array
        $getDefault_id(arrs) {
            return arrs.filter(data => {
                if (Number(data.is_default) === 1) {
                    return data
                }
            })
        },
        $getPrimary_id(arrs) {
            return arrs.filter(data => {
                if (Number(data.is_primary) === 1) {
                    return data
                }
            })
        },
        $isDuplicateItemReturnIndex(newObj) {
            for (let i = 0; i < this.items.length; i++) {
                const currentObj = this.items[i];
                if ((currentObj.product_id === newObj.product_id) &&
                    (currentObj.unit_price === newObj.unit_price) &&
                    (currentObj.warehouse === newObj.warehouse) &&
                    (currentObj.unit === newObj.unit)) {
                    return i;
                }
            }
            return -1;
        },

    }
}