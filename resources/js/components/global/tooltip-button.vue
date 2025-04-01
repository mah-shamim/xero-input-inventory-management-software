<template>
<v-tooltip v-bind:[position]="position">
  <template #activator="{on, attrs}">
    <v-btn
        v-bind:to="to"
        v-bind:[size]="size"
        v-bind:icon="icon"
        v-bind:dark="dark"
        :color="color"
        v-on="on"
        v-bind="attrs"
        :dusk="dusk"
        @click="$emit('click')"
    >
      <v-icon v-if="iconName" v-bind:[size]="size">{{ iconName }}</v-icon>
      <slot></slot>
    </v-btn>
  </template>
  <span>{{ dataText }}</span>
</v-tooltip>
</template>

<script>
export default {
name : "tooltip-button",
props: {
  color   : {
    type   : String,
    default: 'default'
  },
  position: {
    type   : String,
    default: 'top'
  },
  dataText: {
    type    : String,
    required: true
  },
  size    : {
    type   : String,
    default: 'default'
  },
  icon    : {
    type   : Boolean,
    default: false
  },
  dark    : {
    type   : Boolean,
    default: false
  },
  to      : {
    type: Object
  },
  iconName: {
    type: String
  },
  dusk:{
    type:String
  }
}
}
</script>
