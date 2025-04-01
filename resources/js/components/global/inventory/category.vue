<template>
    <div>
        <!--1. broadcasts ids via category_id, or categoryProp-->
        <!--2. checked item configure-->

        <div v-for="cats in categories" v-if="listWithCheckBox == true">
            <input type="checkbox" id="checkedId" :value="cats.id" @click="checkActive(cats.id, $event)">
            <span for="checkedId">{{cats.name}}</span>
            <br>
            <category-lists :categoryProp="cats.children"
                            :indentProp="indent"
                            :category_ids="cat_ids"
                            :style="{ textIndent: indent + 'px' }"
                            v-if="cats.children? cats.children.length>0 : false">

            </category-lists>
        </div>
        <div v-else>
            <ul class="catModify child_list">
                <li class="parent_list" v-for="cats in categories">
                    {{cats.name}}
                    <category-lists :categoryProp="cats.children"
                                    :listWithCheckBox="false"
                                    :isTable="false"
                                    :indentProp="indent"
                                    :category_ids="cat_ids"
                                    v-if="cats.children? cats.children.length>0 : false">

                    </category-lists>
                </li>
            </ul>

        </div>



        <!--<table class="table stripe">-->
            <!--<thead>-->
            <!--<tr>-->
                <!--<th>Name</th>-->
                <!--<th>Description</th>-->
                <!--<th>Type</th>-->
                <!--<th>Child</th>-->
                <!--<th>Action</th>-->
            <!--</tr>-->
            <!--</thead>-->
            <!--<tbody>-->
            <!--<tr v-for="item in categories">-->
                <!--<td>{{item.name}}</td>-->
                <!--<td>{{item.description}}</td>-->
                <!--<td>{{item.type}}</td>-->
                <!--<td>-->
                    <!--<template v-for="childItem in item.children">-->
                        <!--{{childItem.name}} |-->
                    <!--</template>-->
                <!--</td>-->
                <!--<td>-->
                    <!--<a href="">Edit</a>-->
                    <!--<a href="">Delete</a>-->
                <!--</td>-->
            <!--</tr>-->
            <!--</tbody>-->
        <!--</table>-->
    </div>
</template>

<script>
    export default {
        props: {
            categoryProp: {
                type: Array,
                default: () => []
            },
            indentProp: {
                type: Number,
                default: () => 0
            },
            category_ids: {
                type: Array,
                default: () => []
            },
            listWithCheckBox: {
                type: Boolean,
                default: () => true
            },
            isTable: {
                type: Boolean,
                default: () => true
            }
        },
        data() {
            return {
                indent: 15,
                categories: [],
                cat_ids: [],
                showCategoryList: true
            }


        },
        computed: {},
        created() {
            if (this.categoryProp.length === 0) {
                console.log("check")
                axios.get('/api/inventory/categories').then(response => {
                    this.categories = response.data;
                })
            } else {
                this.indent = this.indentProp + 15;
                this.categories = this.categoryProp;
            }
        },
        methods: {
            checkActive(data, event) {
                this.cat_ids = this.category_ids;
                if (event.target.checked) {
                    this.cat_ids.push(data);
                } else {
                    this.cat_ids.pop(data);
                }
                this.$emit('categoryIds', this.cat_ids);
            }
        }
    }
</script>