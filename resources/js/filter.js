import store from "./store";
Vue.filter('findTotalFromArrayByKey',(arr, key=null)=>{
    let total = 0.0
    if(arr.length>0){
        arr.forEach(val=>{
            total += parseFloat(val[key])
        })
        return total
    }else{
        return 0
    }
})


Vue.filter('getPaymentStatusString', (val, total)=>{
    if(val<total || val === total){
        return 'due'
    }
    if(val>total){
        return 'over paid'
    }

})

Vue.filter('uniqueByKeyValue', (arr, key) => {
    return arr.length > 0 ? _.uniqBy(arr, key) : arr;
})

Vue.filter('toDecimalFix',(val, precision)=>{
    return parseFloat(val).toFixed(precision)
})

Vue.filter('deliver_status', (val)=>{
    val = parseInt(val)
    if (val === 1) {
        return 'received'
    }
    if (val === 2) {
        return 'pending'
    }
    if (val === 3) {
        return 'ordered'
    }
})

// single select
Vue.filter('payment_by', (val)=>{
    if(val===1 || val==='1'){
        return 'cash'
    }
    if(val===2 || val ==='2'){
        return 'credit card'
    }
    if(val===3 || val==='3'){
        return 'cheque'
    }else{
        return null
    }
})

Vue.filter('payment_status', val=>{
    if(val===1 || val==='1'){
        return 'paid'
    }
    if(val===2 || val ==='2'){
        return 'partial'
    }
    if(val===3 || val==='3'){
        return 'due'
    }else{
        return null
    }
})

Vue.filter('vuetifyOptionString', (val)=>{
    if(val){
        let page='',
            itemsPerPage='', sortBy='', sortDesc=''
        if(val.hasOwnProperty('page')&&val.page){
            page = 'page='+val.page
        }
        if(val.hasOwnProperty('itemsPerPage') && val.itemsPerPage){
            itemsPerPage = '&itemsPerPage='+val.itemsPerPage
        }
        if(val.hasOwnProperty('sortBy')&&val.sortBy.length){
            sortBy = '&sortBy='+val.sortBy[0]
        }
        if(val.hasOwnProperty('sortDesc')&&val.sortDesc.length){
            sortDesc = '&sortDesc='+val.sortDesc[0]
        }

        return page+itemsPerPage+sortBy+sortDesc.trim()
    }
})

Vue.filter('highlightSearchColor', (words, query)=>{
    var iQuery = new RegExp(query, "ig")
    console.log(iQuery)
    return words.toString().replace(iQuery, function(matchedTxt,a,b){
        return ('<span class=\'highlight\'>' + matchedTxt + '</span>');
    });
})

Vue.filter('toFix', (val, params)=>{
    if(val){
        return parseFloat(val).toFixed(params)
    }else{
        return null
    }
})

Vue.filter('customFilter',(item, queryText, itemText)=>{
    const textOne = item.name.toString().toLowerCase()
    const textTwo = item.code.toString().toLowerCase()
    const searchText = queryText.toString().toLowerCase()
    return textOne.indexOf(searchText) > -1 || textTwo.indexOf(searchText) > -1
})

Vue.filter('obj_string_value', (obj, key)=>{
    const keys = key.split('.');
    let value = obj;
    for (let i = 0; i < keys.length; i++) {
        if (typeof value === 'undefined') {
            return undefined;
        }
        value = value[keys[i]];
        if (typeof value === 'object' && value !== null) {
            value = JSON.stringify(value);
            value = JSON.parse(value);
        }
    }
    return value;
})

Vue.filter('booleanToString', (val)=>{
    return !!val
})

Vue.filter('booleanToNumber', (val)=>{
    return val ? 1 : 0
})

Vue.filter('toUppercase', (val) => {
    if(val) {
        return val.toUpperCase()
    }
})

Vue.filter('firstUpperOnly', (val) => {
    if(val) {
        return _.startCase(_.toLower(val));
    }
})

Vue.filter('sortPartNumber', (part_numbers, product, warehouse_id) => {
    if(product.manufacture_part_number) {
        let items = []
        if(part_numbers.length) {
            part_numbers.forEach(v => {
                if(v.warehouse_id === warehouse_id && v.product_id === product.id) {
                    items.push(v)
                }
            })
        }
        return items
    } else {
        return null
    }
})

Vue.filter('removeTimeFromDate', (value) => {
    if(value) {
        let date = moment(value, store.getters['settings/getDefaultDateFormat'])
        return date.isValid() ? value.split(' ')[0] : value;
    }
})

Vue.filter('delivery_status', (value) => {
    value = parseInt(value)
    if(value === 1)
        return 'received'

    if(value === 2)
        return 'pending'

    if(value === 3)
        return 'ordered'
})

Vue.filter('momentFormatBy', (item, format) => {
    return moment(item).format(format)
})

Vue.filter('momentFormatByWithCurrentFormat', (item, currentFormat = "YYYY-MM-DD", format) => {
    return moment(item, currentFormat).format(format)
})