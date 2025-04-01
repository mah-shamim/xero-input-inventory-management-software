<template>
    <div>
        <div class="custom-toolbar" v-if="toolbar">
            <div>
                <button v-for="button in toolbarAction" class="rounded dark s no-shadow"
                        @click="$emit(button.action, selectedItem)">
                    {{button.name}}
                </button>
            </div>
        </div>

        <a href="#" @click.prevent="printMe" class="printAll">
             Print
        </a>


       <div id="printArea"><br>
           <table class="table ivueTable" v-if="isLoaded">
               <thead>
               <tr>
                   <th v-for="key in gridColumns">
                       {{ key.column | capitalize | replace('_', ' ') }}
                       <a @click="sortBy(key.field)">
                           <Icon type="arrow-down-b"></Icon>
                       </a>
                   </th>
                   <th>Action</th>
               </tr>
               </thead>
               <tbody>
               <tr v-for="( list, pindex ) in filterData" :class="{highlight: setSelected(list)}" @click="selectRaw(list)">
                   <td v-for="( key, gindex ) in gridColumns">
                       {{ simplifyNotation([list, key.field], deepLink, key ) }}
                   </td>
                   <td>
                       <div class="dropdown">
                           <button class="dark s list-table-button">Action &#9662;</button>
                           <div class="dropdown-content bg-dark for-list-table">
                               <router-link :to="{name: model+'Show', params:{id:list.id}}" v-if="showHide">show
                               </router-link>
                               <a>Report</a>
                               <router-link :to="{name: model + 'Edit', params:{id:list.id}}">Edit</router-link>
                               <a @click="deleteItem(list.id)">Delete</a>
                               <payment-component :model="model" :model_id="list.id" :list_data="list"
                                                  :showPayment="showPayment">
                               </payment-component>
                           </div>
                       </div>
                   </td>
               </tr>
               </tbody>
               <tfoot>
               <tr>
                   <td :colspan="gridColumns.length + 1">
                       <pagination class="right"
                                   :data="laravelData"
                                   :limit="5"
                                   v-on:pagination-change-page="getResults">
                       </pagination>
                   </td>

               </tr>
               </tfoot>
           </table>
           <div v-else>
               <Span class="large"></Span>
           </div>
       </div>
    </div>
</template>
<style>
    .list-table-button {
        padding: 11px 4px !important;
        margin: 0 !important;
        line-height: 0 !important;
    }

    .dropdown-content.for-list-table a {
        padding: 0 !important;
    }

    .table > tbody > tr:hover {
        background-color: #2d8cf04a;
        cursor: pointer;
    }

    .table > tbody > tr.highlight {
        background-color: #2d8cf04a;
        cursor: pointer;
    }
</style>

<script>


    export default {

        props: {
            queryString: {
                type: String,
                default: () => ''
            },
            gridColumns: {
                type: Array,
                default: () => []
            },
            model: {
                type: String,
                default: () => ''
            },
            module: {
                type: String,
                default: () => ''
            },
            deepLink: {
                type: Array,
                default: () => []
            },

            showPayment: {
                type: Boolean,
                default: () => false
            },
            showHide: {
                type: Boolean,
                default: () => false
            },
            sortOrder: {
                type: Boolean,
                default: () => true
            },
            sortKey: {
                type: String,
                default: () => ""
            },
            toolbar: {
                type: Boolean,
                default: () => false
            },
            toolbarAction: {
                type: Array,
                default: () => []
            },
            selectable: {
                type: Boolean,
                default: () => false
            }
        },
        data() {
            return {
                laravelData: {},
                lists: [],
                sortKeyData: 'name',
                sortOrderData: true,
                selectKey: 'name',
                searchKey: '',
                selected: undefined,
                previousSelected: undefined,
                selectedItem: null
            }
        },
        watch: {
            queryString: _.debounce(function (val) {
                this.getResults();
            }, 800)
        },
        computed: {
            filterData() {
                return _.orderBy(this.lists, this.sortKeyData, this.sortOrderData ? 'desc' : 'asc');
            },
            isLoaded() {
                return !_.isEmpty(this.laravelData);
            }
        },
        filters: {
            capitalize(str) {
                return _.upperFirst(str);
            },
            replace(str, input, output) {
                return str.replace(input, output);
            }
        },
        created() {
            this.sortKeyData = this.sortKey
            this.sortOrderData = this.sortOrder
            this.getResults();
            this.$eventHub.$on('refresh', this.getResults)

        },
        beforeDestroy() {
            this.$eventHub.$off('refresh')
        }
        ,
        methods: {
            sortBy(key) {
                this.sortKeyData = key
                this.sortOrderData = !this.sortOrderData
            },
            getResults(page, queryString) {
                if (typeof page === 'undefined') {
                    page = 1;
                }
                queryString = this.queryString;
                let url = '/api/' + this.module + '/' + this.model + '?page=' + page + '&sortorder=' + this.sortOrderData + '&sortkey=' + this.sortKeyData + queryString
                this.$root.$data.erp.request.get(url, this, (data) => {
                    this.laravelData = data
                    this.lists = this.laravelData.data
                });
            },
            simplifyNotation(items, deepLink, means=null) {
                let item = items[0];
                let key = items[1];
                if(means.hasOwnProperty('boolean')){
                    return means.boolean[item.status].string
                }
                
                if (deepLink.length == 0) {
                    let arr = [key];
                    if (key === 'created_at') {
                        return moment(item[key]).format("DD-MM-YYYY [at] hh:mm:ss a")
                    }else if(key === 'customer_name' && this.$route.name=='salesIndex'){
                        return item['customer']['user']['name']
                    }
                    else {
                        return item[arr];
                    }
                } else {
                    /*TODO: implement recursive function to find the last value*/
                    for (let i = 0; i < deepLink.length; i++) {
                        for (let j = 0; j < Object.keys(deepLink[i]).length; j++) {
                            let spKey = Object.keys(deepLink[i])[j];
                            if (spKey === key) {
                                let nextNote = deepLink[i][spKey];
                                return item[spKey][nextNote];
                            }
                        }
                    }
                    let arr = [key];
                    if (key === 'created_at') {
                        return moment(item[key]).format("DD-MM-YYYY [at] hh:mm:ss a")
                    } else {
                        return item[arr];
                    }

                }
            },
            getItem(id) {
                return this.lists.find((item) => {
                    return item.id === id
                })
            },
            deleteItem(id) {
                let index = this.lists.indexOf(this.getItem(id))
                if (!confirm("are you sure?")) {
                    return false;
                }

                axios.delete('/api/' + this.module + '/' + this.model + '/' + id).then(response => {
                    if (response.data.type === 'error') {
                        this.$root.showMessage(response.data.message, 'warning')
                    } else {
                        this.$root.showMessage(response.data.message)
                        this.lists.splice(index, 1);
                    }
                }).catch(error => {

                });
            },
            selectRaw(item) {
                if (typeof this.selected != 'undefined') {
                    this.previousSelected = this.selected
                }
                if (this.previousSelected == item.id) {
                    this.selected = undefined
                    this.selectedItem = null
                    this.previousSelected = null
                    return
                }
                this.selected = item.id
                this.selectedItem = item
            },
            setSelected(item) {
                return ((item.id == this.selected) && this.selectable)
            },
            printMe(){
                window.print()
            }
        }
    }
</script>
