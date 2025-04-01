<template>
  <v-tooltip v-bind:[position]="position">
    <template #activator="{on, attrs}">
      <component :is="tagName" v-on="on" v-bind="attrs">
        <slot></slot>
      </component>
    </template>
    <span>{{dataText}}</span>
  </v-tooltip>
</template>

<script>
export default {
  name : "vbn",
  props: {
    dataText:{
      type:String,
      required:true,
    },
    position:{
      type:String,
      default:'top'
    }
  },
  data(){
    return {
      tagName:'span'
    }
  },
  created() {
    this.$slots.default.forEach((item) => {
      this.tagName = item['tag'] ? item['tag'] : 'span';
    });
  }
}
</script>

<style scoped>

</style>
