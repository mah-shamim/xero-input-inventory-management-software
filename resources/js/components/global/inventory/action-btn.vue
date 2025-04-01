<script>
import icons from "../../../ERPFramework/icons";

export default {
  name: "action-btn",
  props:{
    icon:{
      type:String,
      default: `${icons.create}`
    },
    text:{
      type:String,
      default:'Create'
    },
    color:{
      type:String,
      default:'green'
    }
  }
}
</script>
<template>
  <v-btn
      text
      small
      outlined
      class="mr-2"
      :color="color"
      @click="$emit('click')"
  >
    {{ text }}
    <v-icon v-html="`${icon}`"/>
  </v-btn>
</template>