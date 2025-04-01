<script>
export default {
  name: "collapse-btn"
}
</script>

<template>
  <v-btn
      text
      small
      outlined
      class="mr-2"
      color="primary"
      @click="$emit('click')"
  >
    Collapse
    <v-icon>{{ $root.icons.collapse }}</v-icon>
  </v-btn>
</template>

<style scoped>

</style>